<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            $table->string('client_name', 255)->nullable()->after('jo_no');
            $table->string('payee', 255)->nullable()->after('client_name');
            $table->string('rv_cv_no', 120)->nullable()->after('liq_no');
        });
    }

    public function down(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'payee', 'rv_cv_no']);
        });
    }
};
