<?php

/**
 * # Add Maintenance Mode to Global Title
 *
 * This migration inserts a new `maintenance_mode` setting into the `global_title` table.
 * The setting defaults to '0' (off), meaning maintenance mode is disabled.
 * When set to '1', the application should display a maintenance page to users.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * <!-- Insert a new row into `global_title` with key='maintenance_mode' and mark='0' (disabled by default) -->
     */
    public function up(): void
    {
        DB::table('global_title')->insert([
            'key' => 'maintenance_mode',
            'mark' => '0',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * <!-- Remove the `maintenance_mode` row from `global_title` to revert this migration -->
     */
    public function down(): void
    {
        DB::table('global_title')->where('key', 'maintenance_mode')->delete();
    }
};
