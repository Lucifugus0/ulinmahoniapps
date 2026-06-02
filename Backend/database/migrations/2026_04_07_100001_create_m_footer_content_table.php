<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the footer content master table.
 * Stores key-value pairs for footer text sections such as
 * company description and copyright notice, with multilang XML rich text support.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_footer_content', function (Blueprint $table) {
            $table->id('idrec');
            /** Unique identifier key, e.g. 'company_description', 'copyright' */
            $table->string('key', 50)->unique();
            /** Multilang XML rich text content */
            $table->text('value')->nullable();
            /** 1 = active, 0 = inactive */
            $table->tinyInteger('status')->default(1);
            /** Admin user who created this record */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this record */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_footer_content');
    }
};
