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
        $dbName = DB::getDatabaseName();
        $column = DB::selectOne(
            "SELECT DATA_TYPE, COLUMN_KEY, EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'id'",
            [$dbName]
        );

        if ($column && $column->DATA_TYPE !== 'char') {
            $isPrimary = $column->COLUMN_KEY === 'PRI';
            $isAutoIncrement = str_contains((string) $column->EXTRA, 'auto_increment');

            if ($isAutoIncrement) {
                DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NOT NULL');
            }

            if ($isPrimary) {
                DB::statement('ALTER TABLE notifications DROP PRIMARY KEY');
            }

            DB::statement('ALTER TABLE notifications MODIFY id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE notifications ADD PRIMARY KEY (id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration for notification id type change.
    }
};
