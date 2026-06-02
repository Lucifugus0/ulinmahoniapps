<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the footer contacts master table.
 * Stores contact information items displayed in the website footer,
 * each with an optional FontAwesome icon, multilang label, and clickable link.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_footer_contacts', function (Blueprint $table) {
            $table->id('idrec');
            /** FontAwesome icon class, e.g. 'fas fa-phone' */
            $table->string('icon_class', 100)->nullable();
            /** Contact label, multilang XML */
            $table->string('label', 255);
            /** Display text for the contact item */
            $table->string('value', 500);
            /** Clickable URL, e.g. 'mailto:' or 'tel:' */
            $table->string('link_url', 500)->nullable();
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
        Schema::dropIfExists('m_footer_contacts');
    }
};
