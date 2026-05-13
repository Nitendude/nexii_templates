<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const DUPLICATE_DELETED_AT = '2026-03-24 15:00:00';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicateIds = DB::table('clients')
            ->select('id', 'name', 'tin_number')
            ->whereNull('deleted_at')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->whereNotNull('tin_number')
            ->where('tin_number', '!=', '')
            ->orderBy('name')
            ->orderBy('tin_number')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($client) => $client->name . '|' . $client->tin_number)
            ->flatMap(function ($group) {
                if ($group->count() <= 1) {
                    return [];
                }

                return $group->slice(1)->pluck('id');
            })
            ->values()
            ->all();

        if ($duplicateIds === []) {
            return;
        }

        DB::table('clients')
            ->whereIn('id', $duplicateIds)
            ->update([
                'deleted_at' => self::DUPLICATE_DELETED_AT,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('clients')
            ->where('deleted_at', self::DUPLICATE_DELETED_AT)
            ->update([
                'deleted_at' => null,
                'updated_at' => now(),
            ]);
    }
};
