<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_advance_requests', function (Blueprint $table) {
            $table->unsignedSmallInteger('salary_deduction_terms')->nullable()->after('is_personal');
        });

        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('cash_advance_deduction', 12, 2)->default(0)->after('deductions_total');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn('cash_advance_deduction');
        });

        Schema::table('cash_advance_requests', function (Blueprint $table) {
            $table->dropColumn('salary_deduction_terms');
        });
    }
};
