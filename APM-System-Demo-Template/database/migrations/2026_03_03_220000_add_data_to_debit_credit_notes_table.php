<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debit_credit_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('debit_credit_notes', 'data')) {
                $table->json('data')->nullable()->after('remarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('debit_credit_notes', function (Blueprint $table) {
            if (Schema::hasColumn('debit_credit_notes', 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};

