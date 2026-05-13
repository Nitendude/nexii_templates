<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_advance_liquidations', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_advance_liquidations', 'status')) {
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])
                    ->default('Pending')
                    ->after('receipt_paths');
            }

            if (!Schema::hasColumn('cash_advance_liquidations', 'approved_by')) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('cash_advance_liquidations', 'approved_at')) {
                $table->timestamp('approved_at')
                    ->nullable()
                    ->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_advance_liquidations', function (Blueprint $table) {
            if (Schema::hasColumn('cash_advance_liquidations', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('cash_advance_liquidations', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('cash_advance_liquidations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

