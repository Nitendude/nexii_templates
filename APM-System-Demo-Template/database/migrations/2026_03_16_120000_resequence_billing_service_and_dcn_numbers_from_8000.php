<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $billingStatements = DB::table('billing_statements')
                ->select(['id'])
                ->where('document_type', '!=', 'service_invoice')
                ->orderBy('statement_no')
                ->orderBy('id')
                ->get();

            $nextBillingNumber = 8000;
            foreach ($billingStatements as $statement) {
                DB::table('billing_statements')
                    ->where('id', $statement->id)
                    ->update(['statement_no' => $nextBillingNumber++]);
            }

            $serviceInvoices = DB::table('service_invoices')
                ->select(['id'])
                ->orderBy('statement_no')
                ->orderBy('id')
                ->get();

            $nextServiceNumber = 8000;
            foreach ($serviceInvoices as $invoice) {
                DB::table('service_invoices')
                    ->where('id', $invoice->id)
                    ->update(['statement_no' => $nextServiceNumber++]);
            }

            $notes = DB::table('debit_credit_notes')
                ->select(['id', 'note_no'])
                ->get()
                ->sortBy(function (object $note): array {
                    $number = 0;
                    if (preg_match('/(\d+)/', (string) $note->note_no, $matches)) {
                        $number = (int) $matches[1];
                    }

                    return [$number, (int) $note->id];
                })
                ->values();

            $nextNoteNumber = 8000;
            foreach ($notes as $note) {
                DB::table('debit_credit_notes')
                    ->where('id', $note->id)
                    ->update(['note_no' => 'DCN-' . $nextNoteNumber++]);
            }
        });
    }

    public function down(): void
    {
        // Irreversible resequencing migration.
    }
};
