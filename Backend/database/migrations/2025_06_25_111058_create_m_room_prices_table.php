<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_room_prices', function (Blueprint $table) {
            $table->increments('idrec'); 
            $table->string('room_id', 20);
            $table->date('date');
            $table->decimal('price', 18, 4);                       
            $table->tinyInteger('status');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_room_prices');
    }
};
