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
        if (Schema::hasColumn('cash_advance_liquidations', 'receipt_paths')) {
            return;
        }

        Schema::table('cash_advance_liquidations', function (Blueprint $table) {
            $table->json('receipt_paths')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('cash_advance_liquidations', 'receipt_paths')) {
            return;
        }

        Schema::table('cash_advance_liquidations', function (Blueprint $table) {
            $table->dropColumn('receipt_paths');
        });
    }
};
