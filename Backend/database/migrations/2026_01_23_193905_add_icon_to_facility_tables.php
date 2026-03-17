<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_property_facility', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('facility');
        });

        Schema::table('m_room_facility', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('facility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_property_facility', function (Blueprint $table) {
            $table->dropColumn('icon');
        });

        Schema::table('m_room_facility', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
