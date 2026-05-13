<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('billing_statements') || !Schema::hasTable('service_invoices')) {
            return;
        }

        DB::statement("
            INSERT INTO service_invoices (statement_no, job_order_id, created_by_user_id, data, created_at, updated_at)
            SELECT bs.statement_no, bs.job_order_id, bs.created_by_user_id, bs.data, bs.created_at, bs.updated_at
            FROM billing_statements bs
            WHERE bs.document_type = 'service_invoice'
        ");

        DB::statement("
            DELETE FROM billing_statements
            WHERE document_type = 'service_invoice'
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('billing_statements') || !Schema::hasTable('service_invoices')) {
            return;
        }

        DB::statement("
            INSERT INTO billing_statements (statement_no, document_type, job_order_id, created_by_user_id, data, created_at, updated_at)
            SELECT si.statement_no, 'service_invoice', si.job_order_id, si.created_by_user_id, si.data, si.created_at, si.updated_at
            FROM service_invoices si
        ");

        DB::statement("DELETE FROM service_invoices");
    }
};

