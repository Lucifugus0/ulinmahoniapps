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
        Schema::create('t_room_item_conditions', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('order_id', 100);
            $table->integer('booking_id')->nullable();
            $table->string('item_name', 255);
            $table->enum('condition', ['good', 'missing', 'damaged'])->default('good');
            $table->text('custom_text')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('damage_charge', 15, 2)->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_room_item_conditions');
    }
};
