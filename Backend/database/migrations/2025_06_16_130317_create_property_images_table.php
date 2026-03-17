<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_property_images', function (Blueprint $table) {
            $table->increments('idrec');
            $table->string('property_id', 100)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('thumbnail', 100)->nullable();
            $table->string('caption', 200)->nullable();           
            $table->string('created_by', 100)->default('admin');
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE m_property_images MODIFY image MEDIUMBLOB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_property_images');
    }
};
