<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            if (!Schema::hasColumn('reimbursable_voucher_items', 'remarks')) {
                $table->string('remarks', 255)->nullable()->after('rv_cv_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reimbursable_voucher_items', function (Blueprint $table) {
            if (Schema::hasColumn('reimbursable_voucher_items', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
};
