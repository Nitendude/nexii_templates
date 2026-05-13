<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $jobOrdersByNumber = DB::table('job_orders')
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->pluck('consignee', 'number')
            ->mapWithKeys(fn ($consignee, $number) => [trim((string) $number) => trim((string) $consignee)]);

        DB::table('reimbursable_voucher_items')
            ->whereNotNull('jo_no')
            ->orderBy('id')
            ->chunkById(200, function ($items) use ($jobOrdersByNumber): void {
                foreach ($items as $item) {
                    $joNumber = $this->extractJoNumber($item->jo_no);
                    if (!$joNumber || !$jobOrdersByNumber->has($joNumber)) {
                        continue;
                    }

                    $fullClient = trim((string) $jobOrdersByNumber->get($joNumber));
                    $label = $this->voucherClientLabel($fullClient);
                    $current = trim((string) ($item->client_name ?? ''));
                    $firstWord = preg_split('/\s+/', $fullClient)[0] ?? '';

                    if ($label === '' || ($current !== '' && strcasecmp($current, $firstWord) !== 0 && mb_strlen($current) >= mb_strlen($label))) {
                        continue;
                    }

                    DB::table('reimbursable_voucher_items')
                        ->where('id', $item->id)
                        ->update(['client_name' => $label]);
                }
            });
    }

    public function down(): void
    {
        // Data repair only. The previous shortened labels cannot be reliably reconstructed.
    }

    private function extractJoNumber(?string $raw): ?string
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d{3,})$/', $value, $matches)) {
            return $matches[1];
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
};
