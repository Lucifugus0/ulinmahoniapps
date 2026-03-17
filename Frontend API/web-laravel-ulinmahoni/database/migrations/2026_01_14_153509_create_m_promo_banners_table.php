<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_promo_banners', function (Blueprint $table) {
            $table->id('idrec');

            $table->string('title', 255);
            $table->unsignedBigInteger('image_id')->nullable();
            $table->text('descriptions')->nullable();

            // Status (1 = active, 0 = inactive)
            $table->tinyInteger('status')->default(1)->index();

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_promo_banners');
    }
};
