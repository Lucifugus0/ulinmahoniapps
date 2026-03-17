<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_promo_banner_images', function (Blueprint $table) {
            $table->id('idrec');

            $table->unsignedBigInteger('promo_banner_id');
            $table->string('image', 255);
            $table->string('thumbnail', 255)->nullable();
            $table->string('caption', 255)->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Foreign key with cascade delete
            $table->foreign('promo_banner_id')
                ->references('idrec')
                ->on('m_promo_banners')
                ->onDelete('cascade');

            // Indexes
            $table->index('promo_banner_id');
            $table->index('sort_order');
        });

        // Add foreign key to m_promo_banners.image_id after images table exists
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->foreign('image_id')
                ->references('idrec')
                ->on('m_promo_banner_images')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
        });

        Schema::dropIfExists('m_promo_banner_images');
    }
};
