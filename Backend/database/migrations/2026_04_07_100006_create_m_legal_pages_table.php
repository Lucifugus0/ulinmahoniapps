<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the legal pages master table.
 * Stores legal/policy pages such as Terms of Services, Privacy Policy,
 * and Rental Agreement, with multilang XML rich HTML content.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_legal_pages', function (Blueprint $table) {
            $table->id('idrec');
            /** URL-friendly slug, e.g. 'terms-of-services', 'privacy-policy' */
            $table->string('slug', 50)->unique();
            /** Page title, multilang XML */
            $table->string('title', 255)->nullable();
            /** Full page content, multilang XML rich HTML */
            $table->longText('content')->nullable();
            /** 1 = active, 0 = inactive */
            $table->tinyInteger('status')->default(1);
            /** Admin user who created this record */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this record */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_legal_pages');
    }
};
