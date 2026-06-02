<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the footer links master table.
 * Stores navigation links displayed in the website footer,
 * with multilang XML labels and configurable sort order.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_footer_links', function (Blueprint $table) {
            $table->id('idrec');
            /** Link display text, multilang XML */
            $table->string('label', 255);
            /** Target URL for the footer link */
            $table->string('url', 500);
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
        Schema::dropIfExists('m_footer_links');
    }
};
