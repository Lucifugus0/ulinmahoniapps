<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('image', 2048)->nullable();
            $table->string('order');
            $table->date('date');
            $table->string('customer');
            $table->string('total');
            $table->string('status');
            $table->integer('items')->default(0);
            $table->string('location');
            $table->string('type');
            $table->longText('description');
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
