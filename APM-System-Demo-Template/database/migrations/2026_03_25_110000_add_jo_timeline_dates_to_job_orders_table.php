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
        Schema::table('job_orders', function (Blueprint $table) {
            $table->date('demurrage_date')->nullable()->after('eta');
            $table->date('detention_date')->nullable()->after('demurrage_date');
            $table->date('port_storage_date')->nullable()->after('detention_date');
            $table->date('discharge_date')->nullable()->after('port_storage_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn([
                'demurrage_date',
                'detention_date',
                'port_storage_date',
                'discharge_date',
            ]);
        });
    }
};
