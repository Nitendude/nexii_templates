<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get()
            ->each(function ($user): void {
                $name = trim(preg_replace('/\s+/', ' ', (string) $user->name) ?? '');
                if ($name === '') {
                    return;
                }

                $normalized = mb_convert_case(mb_strtolower($name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
                if ($normalized === $name) {
                    return;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'name' => $normalized,
                        'updated_at' => now(),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible data normalization.
    }
};
