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
        // Update existing instance_settings to disable auto-updates
        DB::table('instance_settings')->update([
            'is_auto_update_enabled' => false,
        ]);

        // For new installations, change the default in the table structure
        // Note: This requires altering the column default, but since we're updating existing records above,
        // new records will be created with false by default via model defaults
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-enable auto-updates (optional, for rollback)
        DB::table('instance_settings')->update([
            'is_auto_update_enabled' => true,
        ]);
    }
};
