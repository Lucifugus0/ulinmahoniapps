<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket attachments table.
 * Stores image/file attachments for ticket messages.
 * HEIC images are converted to JPEG on upload.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_attachments', function (Blueprint $table) {
            $table->id();
            /** FK to the message this attachment belongs to */
            $table->unsignedBigInteger('message_id');
            /** Original filename as uploaded by user */
            $table->string('file_name', 255);
            /** Storage path relative to storage/app */
            $table->string('file_path', 500);
            /** MIME type (e.g., image/jpeg) — always jpeg/png after HEIC conversion */
            $table->string('file_type', 100);
            /** File size in bytes */
            $table->unsignedBigInteger('file_size')->default(0);
            /** Thumbnail path for image previews */
            $table->string('thumbnail_path', 500)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('t_ticket_messages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_attachments');
    }
};
