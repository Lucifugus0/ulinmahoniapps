<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the core tickets table for the customer service ticketing system.
 * Each ticket represents a support request from a mobile/web user
 * to either Front Desk (property staff) or HQ Customer Service.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tickets', function (Blueprint $table) {
            $table->id();
            /** Unique ticket reference number (e.g., KOS1-TKT-0001 or HQ-TKT-0001) */
            $table->string('ticket_number', 20)->unique();
            /** Booking order ID — NULL for Suggestion Box tickets that don't require a booking */
            $table->string('order_id', 100)->nullable();
            /** Property ID — NULL for HQ tickets */
            $table->unsignedInteger('property_id')->nullable();
            /** The customer user who created this ticket */
            $table->unsignedBigInteger('user_id');
            /** FK to t_ticket_categories for type + category */
            $table->unsignedBigInteger('category_id');
            /** Denormalized from category for query performance: front_desk or hq */
            $table->enum('recipient_type', ['front_desk', 'hq']);
            /** Short description of the issue */
            $table->string('subject', 255);
            /** Ticket lifecycle status */
            $table->enum('ticket_status', ['open', 'in_progress', 'closed', 'reopened'])->default('open');
            /** Priority level */
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            /** When the ticket was closed */
            $table->timestamp('closed_at')->nullable();
            /** User who closed the ticket */
            $table->unsignedBigInteger('closed_by')->nullable();
            /** When the ticket was last reopened */
            $table->timestamp('reopened_at')->nullable();
            /** Deadline after which the ticket cannot be reopened (closed_at + 7 days) */
            $table->timestamp('reopen_deadline')->nullable();
            /** Timestamp of the most recent message for sorting */
            $table->timestamp('last_message_at')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();

            /** Indexes for common query patterns */
            $table->index('user_id', 'idx_tickets_user');
            $table->index('property_id', 'idx_tickets_property');
            $table->index('order_id', 'idx_tickets_order');
            $table->index('ticket_status', 'idx_tickets_status');
            $table->index('recipient_type', 'idx_tickets_recipient');

            /** Foreign keys */
            $table->foreign('category_id')->references('id')->on('t_ticket_categories');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tickets');
    }
};
