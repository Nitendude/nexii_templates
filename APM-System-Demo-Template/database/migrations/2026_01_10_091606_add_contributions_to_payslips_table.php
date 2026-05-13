<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('pagibig_contribution', 12, 2)->default(0)->after('deductions_total');
            $table->decimal('philhealth_contribution', 12, 2)->default(0)->after('pagibig_contribution');
            $table->decimal('sss_contribution', 12, 2)->default(0)->after('philhealth_contribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['pagibig_contribution', 'philhealth_contribution', 'sss_contribution']);
        });
    }
};
