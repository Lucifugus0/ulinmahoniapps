<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the hero videos master table.
 * Stores video files used as background on the Frontend web portal
 * and Mobile app home pages. Only one video can be active at a time.
 * Constraints: MP4 format, 21:9 aspect ratio, max 100MB.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_hero_videos', function (Blueprint $table) {
            $table->id('idrec');
            /** Display title for admin reference */
            $table->string('title', 255);
            /** Storage path relative to public disk (e.g., hero_videos/filename.mp4) */
            $table->string('file_path', 500);
            /** File size in bytes */
            $table->unsignedBigInteger('file_size')->default(0);
            /** 1 = active (currently displayed), 0 = inactive. Only ONE row should be 1. */
            $table->tinyInteger('status')->default(0);
            /** Admin user who uploaded this video */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this video */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_hero_videos');
    }
};
