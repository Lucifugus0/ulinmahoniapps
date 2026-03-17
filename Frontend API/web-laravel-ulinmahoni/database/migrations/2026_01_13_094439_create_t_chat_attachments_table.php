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
        Schema::create('t_chat_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('file_type', 50);
            $table->integer('file_size');
            $table->enum('attachment_type', ['ktp', 'payment_proof', 'room_photo', 'document', 'other'])->default('other');
            $table->string('thumbnail_path', 500)->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('message_id')->references('id')->on('t_chat_messages')->onDelete('cascade');

            // Indexes
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_chat_attachments');
    }
};
