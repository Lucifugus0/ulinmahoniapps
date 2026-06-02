<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the taglines master table.
 * Stores multiple taglines that are displayed randomly
 * on the Frontend web portal and Mobile app home pages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_taglines', function (Blueprint $table) {
            $table->id('idrec');
            /** Tagline text displayed on home page hero section */
            $table->string('tagline', 255);
            /** 1 = active (included in random pool), 0 = inactive */
            $table->tinyInteger('status')->default(1);
            /** Admin user who created this tagline */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this tagline */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_taglines');
    }
};
