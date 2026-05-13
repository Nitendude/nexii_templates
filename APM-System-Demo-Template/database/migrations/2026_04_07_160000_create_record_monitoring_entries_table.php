<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_monitoring_entries', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->index();
            $table->string('sheet_name')->nullable()->index();
            $table->string('section_name')->nullable();
            $table->string('entry_group')->default('active')->index();
            $table->string('date_text')->nullable();
            $table->string('jo_number')->nullable()->index();
            $table->string('reference_no')->nullable();
            $table->decimal('billing_amount', 15, 2)->default(0);
            $table->decimal('advances_amount', 15, 2)->default(0);
            $table->decimal('advances_paid_amount', 15, 2)->default(0);
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('wht_amount', 15, 2)->default(0);
            $table->decimal('rebate_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('deducted_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->string('cr_no')->nullable();
            $table->string('ar_no')->nullable();
            $table->string('bl_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('email_sent_on')->nullable();
            $table->string('email_acknowledged')->nullable();
            $table->string('billing_received_on')->nullable();
            $table->string('received_by')->nullable();
            $table->string('status_as_of')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_monitoring_entries');
    }
};
