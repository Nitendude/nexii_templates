<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            if (!Schema::hasColumn('reimbursable_voucher_items', 'deduction_type')) {
                $table->string('deduction_type', 20)->default('none')->after('deduct_ca');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            if (Schema::hasColumn('reimbursable_voucher_items', 'deduction_type')) {
                $table->dropColumn('deduction_type');
            }
        });
    }
};
