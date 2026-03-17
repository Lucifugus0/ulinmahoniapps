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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('image', 2048)->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('location');
            $table->integer('orders');
            $table->string('last_order');
            $table->string('spent');
            $table->integer('refunds')->default(0);
            $table->boolean('fav')->default(false);
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
        Schema::dropIfExists('customers');
    }
};
