<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\Client;
use App\Models\DebitCreditNote;
use App\Models\JobOrder;
use App\Models\RecordMonitoringEntry;
use App\Models\ReimbursableVoucherItem;
use App\Models\ServiceInvoice;
use Illuminate\Support\Arr;

class ClientRenamePropagationService
{
    public function propagate(Client $client, string $oldName): void
    {
        $newName = trim((string) $client->name);
        $oldName = trim($oldName);

        if ($newName === '' || $oldName === '' || strcasecmp($newName, $oldName) === 0) {
            return;
        }

        $affectedJobOrders = JobOrder::query()
            ->where('client_id', $client->id)
            ->orWhere('consignee', $oldName)
            ->get(['id', 'number']);

        if ($affectedJobOrders->isEmpty()) {
            return;
        }

        $jobOrderIds = $affectedJobOrders->pluck('id')->filter()->map(fn ($id) => (int) $id)->values();
        $jobNumbers = $affectedJobOrders->pluck('number')
            ->filter(fn ($number) => trim((string) $number) !== '')
            ->map(fn ($number) => trim((string) $number))
            ->values();

        JobOrder::query()
            ->whereIn('id', $jobOrderIds)
            ->update([
                'client_id' => $client->id,
                'consignee' => $newName,
            ]);

        $this->updateBillingStatementData($jobOrderIds, $client, $oldName, $newName);
        $this->updateServiceInvoiceData($jobOrderIds, $client, $oldName, $newName);
        $this->updateDebitCreditNoteData($jobOrderIds, $client, $oldName, $newName);
        $this->updateReimbursableVoucherItems($jobNumbers, $newName);
        $this->updateRecordMonitoringEntries($jobNumbers, $oldName, $newName);
    }

    private function updateBillingStatementData($jobOrderIds, Client $client, string $oldName, string $newName): void
    {
        BillingStatement::query()
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($client, $oldName, $newName): void {
                foreach ($rows as $row) {
                    $data = $this->applyClientData($row->data ?? [], $client, $oldName, $newName);
                    $row->update(['data' => $data]);
                }
            });
    }

    private function updateServiceInvoiceData($jobOrderIds, Client $client, string $oldName, string $newName): void
    {
        ServiceInvoice::query()
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($client, $oldName, $newName): void {
                foreach ($rows as $row) {
                    $data = $this->applyClientData($row->data ?? [], $client, $oldName, $newName);
                    $row->update(['data' => $data]);
                }
            });
    }

    private function updateDebitCreditNoteData($jobOrderIds, Client $client, string $oldName, string $newName): void
    {
        DebitCreditNote::query()
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($client, $oldName, $newName): void {
                foreach ($rows as $row) {
                    $data = $this->applyClientData($row->data ?? [], $client, $oldName, $newName);
                    $row->update(['data' => $data]);
                }
            });
    }

    private function applyClientData(array $data, Client $client, string $oldName, string $newName): array
    {
        $billBusinessStyle = trim((string) ($data['bill_business_style'] ?? ''));
        $newBusinessStyle = trim((string) ($client->business_style ?? ''));

        $data['bill_to'] = $newName;
        if (array_key_exists('si_registered_name', $data)) {
            $data['si_registered_name'] = $newName;
        }

        if ($newBusinessStyle !== '') {
            $data['bill_business_style'] = $newBusinessStyle;
        } elseif ($billBusinessStyle === '' || strcasecmp($billBusinessStyle, $oldName) === 0) {
            $data['bill_business_style'] = $newName;
        }

        return $data;
    }

    private function updateReimbursableVoucherItems($jobNumbers, string $newName): void
    {
        if ($jobNumbers->isEmpty()) {
            return;
        }

        $jobNumberSet = array_fill_keys($jobNumbers->all(), true);
        $displayName = $this->voucherClientLabel($newName);

        ReimbursableVoucherItem::query()
            ->whereNotNull('jo_no')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($jobNumberSet, $displayName): void {
                foreach ($rows as $row) {
                    $jobNumber = $this->extractJoNumber($row->jo_no);
                    if (!$jobNumber || !isset($jobNumberSet[$jobNumber])) {
                        continue;
                    }

                    if (trim((string) $row->client_name) === $displayName) {
                        continue;
                    }

                    $row->update(['client_name' => $displayName]);
                }
            });
    }

    private function updateRecordMonitoringEntries($jobNumbers, string $oldName, string $newName): void
    {
        if ($jobNumbers->isNotEmpty()) {
            RecordMonitoringEntry::query()
                ->whereIn('jo_number', $jobNumbers)
                ->update(['client_name' => $newName]);
        }

        RecordMonitoringEntry::query()
            ->where('client_name', $oldName)
            ->update(['client_name' => $newName]);
    }

    private function extractJoNumber(?string $raw): ?string
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d{3,})$/', $value, $matches)) {
            return Arr::get($matches, 1);
        }

        return null;
    }

    private function voucherClientLabel(?string $clientName): string
    {
        $clientName = trim((string) $clientName);
        if ($clientName === '' || mb_strlen($clientName) <= 20) {
            return $clientName;
        }

        $words = preg_split('/\s+/', $clientName) ?: [];
        $words = array_values(array_filter($words, fn ($word) => trim((string) $word) !== ''));

        return trim(implode(' ', array_slice($words, 0, 2))) ?: $clientName;
    }
}

