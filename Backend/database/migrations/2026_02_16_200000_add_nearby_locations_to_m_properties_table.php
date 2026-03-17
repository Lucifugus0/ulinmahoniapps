<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->longText('nearby_locations')->nullable()->after('amenities');
        });
    }

    public function down(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->dropColumn('nearby_locations');
        });
    }
};
