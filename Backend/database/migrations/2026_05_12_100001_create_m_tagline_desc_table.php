<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the tagline descriptions master table.
 * Stores multiple tagline descriptions that are displayed randomly
 * on the Frontend web portal and Mobile app home pages,
 * paired with taglines from m_taglines.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_tagline_desc', function (Blueprint $table) {
            $table->id('idrec');
            /** Tagline description text displayed on home page hero section */
            $table->text('description');
            /** 1 = active (included in random pool), 0 = inactive */
            $table->tinyInteger('status')->default(1);
            /** Admin user who created this tagline description */
            $table->unsignedBigInteger('created_by')->nullable();
            /** Admin user who last updated this tagline description */
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_tagline_desc');
    }
};
