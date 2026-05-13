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
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('mo')->nullable();
            $table->string('number')->nullable();
            $table->date('jo_date')->nullable();
            $table->string('costing')->nullable();
            $table->string('co')->nullable();
            $table->string('port')->nullable();
            $table->date('eta')->nullable();
            $table->string('status')->nullable();
            $table->date('date_delivered')->nullable();
            $table->string('consignee')->nullable();
            $table->string('shipper')->nullable();
            $table->text('description')->nullable();
            $table->string('origin')->nullable();
            $table->string('po_number')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('sys')->nullable();
            $table->string('vessel_voyage_no')->nullable();
            $table->date('date_of_arrival')->nullable();
            $table->string('bl_awb_no')->nullable();
            $table->string('no_of_container')->nullable();
            $table->string('shipping_lines')->nullable();
            $table->string('no_of_packages')->nullable();
            $table->string('kind_of_packages')->nullable();
            $table->decimal('gross_weight', 12, 2)->nullable();
            $table->decimal('no_of_cbm', 12, 2)->nullable();
            $table->string('entry_no')->nullable();
            $table->decimal('ctnr_deposit', 12, 2)->nullable();
            $table->date('date_refunded')->nullable();
            $table->text('remarks_location')->nullable();
            $table->string('co_loader_forwarder')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
