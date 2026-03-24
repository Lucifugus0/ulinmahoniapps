<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the m_cities master table for managing city/location data.
     */
    public function up(): void
    {
        Schema::create('m_cities', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('city_name');
            $table->string('province');
            $table->string('slug')->unique();
            $table->string('status')->default('1');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migration — drop the m_cities table.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_cities');
    }
};
