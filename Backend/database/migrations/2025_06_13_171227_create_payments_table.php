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
        Schema::create('t_payment', function (Blueprint $table) {
            $table->increments('idrec');            
            $table->string('order_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->decimal('grandtotal_price', 18, 4)->nullable();
            $table->integer('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_status', 100)->nullable();            
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_payment');
    }
};
