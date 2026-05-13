<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\DebitCreditNote;
use App\Models\JobOrder;
use App\Models\RecordMonitoringEntry;
use App\Models\ServiceInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RecordMonitoringSyncService
{
    public const STATUS_PRESETS = [
        'UPDATED SOA SENT',
        'FOR FOLLOW-UP',
        'PAID',
        'OVERDUE',
        'RECEIVED',
    ];

    public function syncBillingStatement(BillingStatement $statement): RecordMonitoringEntry
    {
        $statement->loadMissing(['jobOrder.assignedUser', 'createdBy']);

        $jobOrder = $statement->jobOrder;
        $data = $statement->data ?? [];
        $entry = $this->findExistingEntry('billing_statement', $statement->id, $jobOrder?->number, (string) $statement->statement_no);

        $entry->fill([
            'source_type' => 'billing_statement',
            'source_id' => $statement->id,
            'client_name' => $jobOrder?->consignee ?: (string) ($data['bill_to'] ?? 'Unknown Client'),
            'sheet_name' => 'SYSTEM GENERATED',
            'section_name' => 'Billing Statement',
            'entry_group' => $entry->entry_group ?: 'active',
            'in_charge' => $this->resolveInCharge($jobOrder, $statement->createdBy),
            'date_text' => $this->normalizeDateText($data['statement_date'] ?? null),
            'jo_number' => $jobOrder?->number,
            'reference_no' => (string) $statement->statement_no,
            'billing_amount' => $this->billingGrandTotal($data),
            'advances_amount' => $this->advanceAdjustmentForJobOrder($statement->job_order_id),
            'advances_paid_amount' => $this->advanceAdjustmentForJobOrder($statement->job_order_id),
            'bl_no' => (string) ($data['bl_no'] ?? $jobOrder?->bl_awb_no ?? ''),
            'raw_data' => array_merge($entry->raw_data ?? [], [
                'document_type' => 'billing_statement',
                'document_label' => 'Billing Statement',
            ]),
        ]);

        $this->applyComputedBalance($entry);
        $entry->save();

        return $entry;
    }

    public function syncServiceInvoice(ServiceInvoice $serviceInvoice): RecordMonitoringEntry
    {
        $serviceInvoice->loadMissing(['jobOrder.assignedUser', 'createdBy']);

        $jobOrder = $serviceInvoice->jobOrder;
        $data = $serviceInvoice->data ?? [];
        $entry = $this->findExistingEntry('service_invoice', $serviceInvoice->id, $jobOrder?->number, (string) $serviceInvoice->statement_no);

        $entry->fill([
            'source_type' => 'service_invoice',
            'source_id' => $serviceInvoice->id,
            'client_name' => $jobOrder?->consignee ?: (string) ($data['bill_to'] ?? 'Unknown Client'),
            'sheet_name' => 'SYSTEM GENERATED',
            'section_name' => 'Service Invoice',
            'entry_group' => $entry->entry_group ?: 'active',
            'in_charge' => $this->resolveInCharge($jobOrder, $serviceInvoice->createdBy),
            'date_text' => $this->normalizeDateText($data['statement_date'] ?? null),
            'jo_number' => $jobOrder?->number,
            'reference_no' => (string) $serviceInvoice->statement_no,
            'billing_amount' => $this->serviceInvoiceTotal($data),
            'advances_amount' => 0,
            'advances_paid_amount' => 0,
            'bl_no' => (string) ($data['bl_no'] ?? $jobOrder?->bl_awb_no ?? ''),
            'raw_data' => array_merge($entry->raw_data ?? [], [
                'document_type' => 'service_invoice',
                'document_label' => 'Service Invoice',
            ]),
        ]);

        $this->applyComputedBalance($entry);
        $entry->save();

        return $entry;
    }

    public function syncDebitCreditNote(DebitCreditNote $note): RecordMonitoringEntry
    {
        $note->loadMissing(['jobOrder.assignedUser', 'createdBy']);

        $jobOrder = $note->jobOrder;
        $data = $note->data ?? [];
        $totals = $this->noteTotals($note);
        $entry = $this->findExistingEntry('debit_credit_note', $note->id, $jobOrder?->number, (string) $note->note_no);

        $entry->fill([
            'source_type' => 'debit_credit_note',
            'source_id' => $note->id,
            'client_name' => $jobOrder?->consignee ?: (string) ($data['bill_to'] ?? 'Unknown Client'),
            'sheet_name' => 'SYSTEM GENERATED',
            'section_name' => 'Debit / Credit Note',
            'entry_group' => $entry->entry_group ?: 'active',
            'in_charge' => $this->resolveInCharge($jobOrder, $note->createdBy),
            'date_text' => $this->normalizeDateText($note->note_date),
            'jo_number' => $jobOrder?->number,
            'reference_no' => (string) $note->note_no,
            'billing_amount' => $totals['debit_total'],
            'advances_amount' => $totals['advances_total'],
            'advances_paid_amount' => 0,
            'deducted_amount' => $totals['credit_total'],
            'bl_no' => (string) ($data['bl_no'] ?? $jobOrder?->bl_awb_no ?? ''),
            'remarks' => $note->remarks ?: $entry->remarks,
            'raw_data' => array_merge($entry->raw_data ?? [], [
                'document_type' => 'debit_credit_note',
                'document_label' => 'Debit / Credit Note',
            ]),
        ]);

        $this->applyComputedBalance($entry);
        $entry->save();

        $this->resyncReceivableDocumentsForJobOrder($note->job_order_id);

        return $entry;
    }

    public function syncImportedRows(array $rows): array
    {
        $seenIds = [];

        foreach ($rows as $row) {
            $match = [
                'source_type' => 'workbook',
                'sheet_name' => $row['sheet_name'],
                'entry_group' => $row['entry_group'],
                'client_name' => $row['client_name'],
                'jo_number' => $row['jo_number'],
                'reference_no' => $row['reference_no'],
                'date_text' => $row['date_text'],
                'bl_no' => $row['bl_no'],
            ];

            $entry = RecordMonitoringEntry::query()->firstOrNew($match);
            $entry->fill(array_merge($row, [
                'source_type' => 'workbook',
            ]));
            $entry->save();
            $seenIds[] = $entry->id;
        }

        return $seenIds;
    }

    public function applyManualFollowUp(RecordMonitoringEntry $entry, array $updates): RecordMonitoringEntry
    {
        $entry->fill($updates);
        $this->applyComputedBalance($entry);
        $entry->save();

        return $entry;
    }

    public function calculateBalance(array|RecordMonitoringEntry $payload): float
    {
        $data = $payload instanceof RecordMonitoringEntry ? $payload->toArray() : $payload;

        $billing = (float) ($data['billing_amount'] ?? 0);
        $advancesPaid = (float) ($data['advances_paid_amount'] ?? 0);
        $payment = (float) ($data['payment_amount'] ?? 0);
        $wht = (float) ($data['wht_amount'] ?? 0);
        $discount = (float) ($data['discount_amount'] ?? 0);
        $rebate = (float) ($data['rebate_amount'] ?? 0);
        $deducted = (float) ($data['deducted_amount'] ?? 0);

        return max(0, round($billing - $advancesPaid - $payment - $wht - $discount - $rebate - $deducted, 2));
    }

    public function isOverdue(RecordMonitoringEntry $entry): bool
    {
        if ((float) $entry->balance_amount <= 0) {
            return false;
        }

        $parsed = $this->parseDateText($entry->date_text);
        if (!$parsed) {
            return false;
        }

        return $parsed->lt(now()->subDays(30));
    }

    private function resyncReceivableDocumentsForJobOrder(?int $jobOrderId): void
    {
        if (!$jobOrderId) {
            return;
        }

        BillingStatement::query()
            ->where('job_order_id', $jobOrderId)
            ->where('document_type', '!=', 'service_invoice')
            ->get()
            ->each(fn (BillingStatement $statement) => $this->syncBillingStatement($statement));

        ServiceInvoice::query()
            ->where('job_order_id', $jobOrderId)
            ->get()
            ->each(fn (ServiceInvoice $invoice) => $this->syncServiceInvoice($invoice));
    }

    private function findExistingEntry(string $sourceType, int $sourceId, ?string $joNumber, string $referenceNo): RecordMonitoringEntry
    {
        return RecordMonitoringEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first()
            ?? RecordMonitoringEntry::query()
                ->whereIn('source_type', [null, 'manual', 'workbook'])
                ->when($joNumber, fn ($query) => $query->where('jo_number', $joNumber))
                ->where('reference_no', $referenceNo)
                ->orderByDesc('id')
                ->first()
            ?? new RecordMonitoringEntry([
                'entry_group' => 'active',
            ]);
    }

    private function billingGrandTotal(array $data): float
    {
        if (is_numeric($data['grand_total'] ?? null)) {
            return (float) $data['grand_total'];
        }

        $non = collect($data['non_receipted_amount'] ?? [])->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);
        $rec = collect($data['receipted_amount'] ?? [])->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);

        return (float) ($non + $rec);
    }

    private function serviceInvoiceTotal(array $data): float
    {
        if (is_numeric($data['si_total_amount_due'] ?? null)) {
            return (float) $data['si_total_amount_due'];
        }

        if (is_numeric($data['si_amount_net_vat'] ?? null)) {
            return (float) $data['si_amount_net_vat'];
        }

        return $this->billingGrandTotal($data);
    }

    private function noteTotals(DebitCreditNote $note): array
    {
        $rows = collect($note->data['rows'] ?? []);
        $debitTotal = 0.0;
        $creditTotal = 0.0;
        $advancesTotal = 0.0;

        foreach ($rows as $row) {
            $side = strtolower((string) ($row['side'] ?? 'debit'));
            $particular = strtoupper(trim((string) ($row['particular'] ?? '')));
            $amount = is_numeric($row['amount'] ?? null) ? (float) $row['amount'] : 0.0;

            if ($side === 'credit') {
                $creditTotal += $amount;
            } else {
                $debitTotal += $amount;
            }

            if ($particular !== '' && str_contains($particular, 'ADVANCE')) {
                $advancesTotal += $amount;
            }
        }

        return [
            'debit_total' => $debitTotal,
            'credit_total' => $creditTotal,
            'advances_total' => $advancesTotal,
        ];
    }

    private function advanceAdjustmentForJobOrder(?int $jobOrderId): float
    {
        if (!$jobOrderId) {
            return 0.0;
        }

        return DebitCreditNote::query()
            ->where('job_order_id', $jobOrderId)
            ->get()
            ->sum(function (DebitCreditNote $note): float {
                return $this->noteTotals($note)['advances_total'];
            });
    }

    private function resolveInCharge(?JobOrder $jobOrder, ?User $createdBy): ?string
    {
        return $jobOrder?->assignedUser?->name ?: $createdBy?->name;
    }

    private function normalizeDateText(mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->format('m/d/Y');
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('m/d/Y');
        }

        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        try {
            return Carbon::parse($value)->format('m/d/Y');
        } catch (\Throwable) {
            return $value;
        }
    }

    private function parseDateText(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        foreach (['m/d/Y', 'm-d-y', 'm-d-Y', 'M d Y', 'M d, Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value));
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function applyComputedBalance(RecordMonitoringEntry $entry): void
    {
        $entry->balance_amount = $this->calculateBalance($entry);

        $status = Str::upper(trim((string) $entry->status_as_of));
        if ($entry->balance_amount <= 0 && ($entry->payment_amount > 0 || $status === 'PAID')) {
            $entry->entry_group = 'paid';
        } elseif ($entry->entry_group !== 'paid') {
            $entry->entry_group = 'active';
        }
    }
}
