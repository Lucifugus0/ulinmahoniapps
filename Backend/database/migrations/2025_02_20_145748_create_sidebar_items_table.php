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
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('route')->nullable();
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('sidebar_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_items');
    }
};
