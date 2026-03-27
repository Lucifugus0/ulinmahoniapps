<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket categories lookup table.
 * Each row defines a ticket type + category combination
 * (e.g., complaint/wifi, booking/payment) with routing info.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_categories', function (Blueprint $table) {
            $table->id();
            /** Ticket type group: booking, complaint, or suggestion */
            $table->string('ticket_type', 30);
            /** Specific category within the type (e.g., payment, wifi, suggestion_box) */
            $table->string('category', 50);
            /** Display labels for each supported language */
            $table->string('label_en', 100);
            $table->string('label_id', 100);
            $table->string('label_zh', 100)->default('');
            /** Where this ticket type routes: front_desk (property staff) or hq (Customer Service HQ) */
            $table->enum('recipient_type', ['front_desk', 'hq']);
            /** Whether a paid booking is required to create this ticket type (0 for Suggestion Box) */
            $table->tinyInteger('requires_booking')->default(1);
            /** Display ordering in the UI */
            $table->integer('sort_order')->default(0);
            /** Active/inactive flag */
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->unique(['ticket_type', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_categories');
    }
};
