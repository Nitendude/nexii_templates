<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('record_monitoring_entries', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('id')->index();
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type')->index();
            $table->string('in_charge')->nullable()->after('entry_group')->index();
        });
    }

    public function down(): void
    {
        Schema::table('record_monitoring_entries', function (Blueprint $table) {
            $table->dropColumn([
                'source_type',
                'source_id',
                'in_charge',
            ]);
        });
    }
};
