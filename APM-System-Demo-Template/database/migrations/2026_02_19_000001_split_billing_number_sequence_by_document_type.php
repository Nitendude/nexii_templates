<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->string('document_type', 32)->default('billing_statement')->after('statement_no');
        });

        DB::statement("
            UPDATE billing_statements
            SET document_type = COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(data, '$.document_type')),
                'billing_statement'
            )
        ");

        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropUnique('billing_statements_statement_no_unique');
            $table->unique(['document_type', 'statement_no'], 'billing_doc_type_statement_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('billing_statements', function (Blueprint $table) {
            $table->dropUnique('billing_doc_type_statement_no_unique');
            $table->unique('statement_no');
            $table->dropColumn('document_type');
        });
    }
};

