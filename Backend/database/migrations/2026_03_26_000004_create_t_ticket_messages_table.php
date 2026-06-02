<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket messages table.
 * Stores all messages within a ticket conversation,
 * including text, image, and system messages (status changes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_messages', function (Blueprint $table) {
            $table->id();
            /** FK to the parent ticket */
            $table->unsignedBigInteger('ticket_id');
            /** FK to the user who sent this message */
            $table->unsignedBigInteger('sender_id');
            /** Message content — NULL for image-only messages */
            $table->text('message_text')->nullable();
            /** Message type: text, image, or system (auto-generated status change messages) */
            $table->enum('message_type', ['text', 'image', 'system'])->default('text');
            /** Whether this message has been edited */
            $table->tinyInteger('is_edited')->default(0);
            /** Timestamp of last edit */
            $table->timestamp('edited_at')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();

            /** Composite index for fetching messages in chronological order */
            $table->index(['ticket_id', 'created_at'], 'idx_ticket_messages_ticket');

            $table->foreign('ticket_id')->references('id')->on('t_tickets')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_messages');
    }
};
