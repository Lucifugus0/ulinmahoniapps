<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds mobile_image column to m_promo_banner_images.
 * Stores a separate banner image optimized for mobile app display (16:9, 1080x608px).
 * Falls back to the main image if mobile_image is null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_promo_banner_images', function (Blueprint $table) {
            $table->string('mobile_image', 255)->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('m_promo_banner_images', function (Blueprint $table) {
            $table->dropColumn('mobile_image');
        });
    }
};
