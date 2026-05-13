<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursable_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 20)->unique();
            $table->string('payee', 80)->nullable();
            $table->date('voucher_date');
            $table->string('ref_no', 120)->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('amount_in_words', 255)->nullable();
            $table->string('prepared_by', 120)->nullable();
            $table->string('approved_by', 120)->nullable();
            $table->string('received_payment', 120)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('reimbursable_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursable_voucher_id')->constrained('reimbursable_vouchers')->cascadeOnDelete();
            $table->unsignedInteger('line_no')->default(1);
            $table->string('jo_no', 120)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('liq_no', 120)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursable_voucher_items');
        Schema::dropIfExists('reimbursable_vouchers');
    }
};

