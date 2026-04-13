<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the footer social media master table.
 * Stores social media links displayed in the website footer,
 * supporting both FontAwesome icons and custom uploaded icon images.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_footer_socials', function (Blueprint $table) {
            $table->id('idrec');
            /** Social media platform name, e.g. 'Instagram', 'TikTok' */
            $table->string('name', 100);
            /** FontAwesome icon class, e.g. 'fab fa-instagram' */
            $table->string('icon_class', 100)->nullable();
            /** Custom icon image URL for platforms without FontAwesome icons */
            $table->string('icon_image', 500)->nullable();
            /** Social media profile URL */
            $table->string('url', 500);
            /** CSS class for hover color effect */
            $table->string('hover_color', 50)->nullable();
            /** Display order, lower numbers appear first */
            $table->integer('sort_order')->default(0);
            /** 1 = active, 0 = inactive */
            $table->tinyInteger('status')->default(1);
            /** Admin user who created this record */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this record */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_footer_socials');
    }
};
