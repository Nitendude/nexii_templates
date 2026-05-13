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
        Schema::table('cash_advance_requests', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('approved_at');
            $table->string('paid_proof_path')->nullable()->after('paid_at');
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete()->after('paid_proof_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_advance_requests', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn(['paid_at', 'paid_proof_path', 'paid_by']);
        });
    }
};
