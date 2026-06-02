<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds min_app_version to global_title table.
 * Mobile apps below this version will be forced to update via store.
 */
return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('global_title')->whereRaw('`key` = ?', ['min_app_version'])->exists();
        if (!$exists) {
            DB::table('global_title')->insert([
                'idrec' => DB::table('global_title')->max('idrec') + 1,
                'key' => 'min_app_version',
                'mark' => '2.0.11',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('global_title')->whereRaw('`key` = ?', ['min_app_version'])->delete();
    }
};
