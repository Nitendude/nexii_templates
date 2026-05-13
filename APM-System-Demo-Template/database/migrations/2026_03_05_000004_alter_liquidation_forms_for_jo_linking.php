<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('liquidation_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('liquidation_forms', 'form_no')) {
                $table->string('form_no', 20)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('liquidation_forms', 'cash_advance_request_id')) {
                $table->foreignId('cash_advance_request_id')->nullable()->after('form_no')->constrained('cash_advance_requests')->nullOnDelete();
            }
            if (!Schema::hasColumn('liquidation_forms', 'cash_advance_item_id')) {
                $table->foreignId('cash_advance_item_id')->nullable()->after('cash_advance_request_id')->constrained('cash_advance_items')->nullOnDelete();
            }
            if (!Schema::hasColumn('liquidation_forms', 'client_name')) {
                $table->string('client_name', 255)->nullable()->after('jo_number');
            }
            if (!Schema::hasColumn('liquidation_forms', 'line_items')) {
                $table->json('line_items')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('liquidation_forms', 'posted_cash_advance_liquidation_id')) {
                $table->foreignId('posted_cash_advance_liquidation_id')->nullable()->after('approved_at')->constrained('cash_advance_liquidations')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('liquidation_forms', function (Blueprint $table) {
            if (Schema::hasColumn('liquidation_forms', 'posted_cash_advance_liquidation_id')) {
                $table->dropConstrainedForeignId('posted_cash_advance_liquidation_id');
            }
            if (Schema::hasColumn('liquidation_forms', 'line_items')) {
                $table->dropColumn('line_items');
            }
            if (Schema::hasColumn('liquidation_forms', 'client_name')) {
                $table->dropColumn('client_name');
            }
            if (Schema::hasColumn('liquidation_forms', 'cash_advance_item_id')) {
                $table->dropConstrainedForeignId('cash_advance_item_id');
            }
            if (Schema::hasColumn('liquidation_forms', 'cash_advance_request_id')) {
                $table->dropConstrainedForeignId('cash_advance_request_id');
            }
            if (Schema::hasColumn('liquidation_forms', 'form_no')) {
                $table->dropColumn('form_no');
            }
        });
    }
};

