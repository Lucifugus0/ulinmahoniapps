<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_refund', function (Blueprint $table) {
            $table->id();
            $table->text('id_booking');
            $table->string('status')->default('Pending');
            $table->text('reason')->nullable();
            $table->decimal('amount', 18, 4)->nullable();
            $table->string('img')->nullable(); 
            $table->string('image_caption')->nullable();
            $table->string('image_path')->nullable();
            $table->dateTime('refund_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_refund');
    }
};
