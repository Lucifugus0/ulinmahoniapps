<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the footer payment methods master table.
 * Stores accepted payment method icons displayed in the website footer,
 * such as BCA, BNI, BRI, Mandiri, QRIS, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_footer_payments', function (Blueprint $table) {
            $table->id('idrec');
            /** Payment method name, e.g. 'BCA', 'QRIS' */
            $table->string('name', 100);
            /** Uploaded payment method icon/logo image path */
            $table->string('icon_image', 500)->nullable();
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
        Schema::dropIfExists('m_footer_payments');
    }
};
