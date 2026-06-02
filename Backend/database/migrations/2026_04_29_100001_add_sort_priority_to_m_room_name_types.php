<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add sort_priority to m_room_name_types so admins can control the
     * order room types appear in the public Frontend's "Kamar Tersedia"
     * section and the Mobile App's room-name filter dropdown.
     */
    public function up(): void
    {
        Schema::table('m_room_name_types', function (Blueprint $table) {
            $table->unsignedInteger('sort_priority')->default(0)->after('name');
        });

        // Backfill existing rows with sequential priority based on idrec order
        $rows = DB::table('m_room_name_types')->orderBy('idrec')->get();
        $i = 1;
        foreach ($rows as $row) {
            DB::table('m_room_name_types')
                ->where('idrec', $row->idrec)
                ->update(['sort_priority' => $i++]);
        }
    }

    public function down(): void
    {
        Schema::table('m_room_name_types', function (Blueprint $table) {
            $table->dropColumn('sort_priority');
        });
    }
};
