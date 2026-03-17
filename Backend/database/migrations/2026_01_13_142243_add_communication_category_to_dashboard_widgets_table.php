<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'communication' to the category enum
        DB::statement("ALTER TABLE dashboard_widgets MODIFY COLUMN category ENUM('stats', 'finance', 'rooms', 'reports', 'communication') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete widgets with 'communication' category before modifying enum
        DB::table('dashboard_widgets')->where('category', 'communication')->delete();

        // Remove 'communication' from the category enum
        DB::statement("ALTER TABLE dashboard_widgets MODIFY COLUMN category ENUM('stats', 'finance', 'rooms', 'reports') NOT NULL");
    }
};
