<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reimbursable_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('reimbursable_vouchers', 'status')) {
                $table->string('status', 20)->default('active')->after('voucher_no');
            }

            if (!Schema::hasColumn('reimbursable_vouchers', 'cancelled_voucher_no')) {
                $table->string('cancelled_voucher_no', 20)->nullable()->after('voucher_no');
                $table->index('cancelled_voucher_no');
            }

            if (!Schema::hasColumn('reimbursable_vouchers', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('reimbursable_vouchers', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reimbursable_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('reimbursable_vouchers', 'cancelled_by')) {
                $table->dropConstrainedForeignId('cancelled_by');
            }

            if (Schema::hasColumn('reimbursable_vouchers', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }

            if (Schema::hasColumn('reimbursable_vouchers', 'cancelled_voucher_no')) {
                $table->dropIndex(['cancelled_voucher_no']);
                $table->dropColumn('cancelled_voucher_no');
            }

            if (Schema::hasColumn('reimbursable_vouchers', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

