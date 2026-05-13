<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('liquidation_forms');
    }

    public function down(): void
    {
        // Old Liquidation Form feature was removed from the system.
    }
};
