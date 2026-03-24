<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the m_calendar_dates table for global date classifications
     * (holiday, high_season, low_season) that apply to ALL rooms.
     */
    public function up(): void
    {
        Schema::create('m_calendar_dates', function (Blueprint $table) {
            $table->increments('idrec');
            $table->date('date')->unique();
            $table->string('date_type', 20); // holiday, high_season, low_season
            $table->string('label', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->index('date_type');
        });
    }

    /**
     * Drop the m_calendar_dates table.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_calendar_dates');
    }
};
