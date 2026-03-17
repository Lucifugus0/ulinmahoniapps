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
        Schema::create('t_booking', function (Blueprint $table) {
            $table->id('idrec');
            $table->integer('property_id')->nullable();
            $table->string('order_id', 100);
            $table->string('user_name', 255)->nullable();
            $table->string('user_email', 255)->nullable();
            $table->string('user_phone_number', 50)->nullable();
            $table->string('room_id', 255);
            $table->dateTime('check_in_at')->nullable();
            $table->binary('ktp_img')->nullable();
            $table->string('doc_type', 50)->nullable();
            $table->string('doc_path', 255)->nullable();            
            $table->dateTime('check_out_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('reason')->nullable();
            $table->text('description')->nullable();
            $table->integer('is_printed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_booking');
    }
};
