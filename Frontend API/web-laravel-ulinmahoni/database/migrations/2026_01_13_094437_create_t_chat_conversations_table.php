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
        Schema::create('t_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100);
            $table->integer('property_id');
            $table->string('title', 255)->nullable();
            $table->enum('status', ['active', 'archived', 'closed'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('property_id');
            $table->index('status');
            $table->index('last_message_at');

            // Foreign keys (using indexes for string-based FKs)
            // Note: Direct FK on string fields can be problematic, relying on indexes instead
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_chat_conversations');
    }
};
