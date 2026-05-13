<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debit_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_no')->unique();
            $table->foreignId('job_order_id')->constrained('job_orders')->cascadeOnDelete();
            $table->enum('note_type', ['debit', 'credit']);
            $table->date('note_date');
            $table->decimal('amount', 12, 2);
            $table->string('description', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debit_credit_notes');
    }
};

