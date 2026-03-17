<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_rooms_door_lock', function (Blueprint $table) {
            $table->id('idrec');
            $table->unsignedInteger('room_idrec');

            $table->bigInteger('lock_id')->unique();
            $table->string('lock_alias', 100)->nullable();

            $table->string('lock_mac', 50)->nullable();
            $table->string('model_num', 50)->nullable();
            $table->string('firmware_revision', 50)->nullable();

            $table->integer('battery_level')->nullable();
            $table->boolean('has_gateway')->default(false);

            $table->smallInteger('lock_sound')->nullable();
            $table->smallInteger('privacy_lock')->nullable();
            $table->smallInteger('is_frozen')->nullable();
            $table->smallInteger('passage_mode')->nullable();

            $table->bigInteger('last_sync_at')->nullable();

            // Passcode fields
            $table->string('passcode', 50)->nullable();
            $table->string('passcode_name', 100)->nullable();
            $table->bigInteger('passcode_start')->nullable();
            $table->bigInteger('passcode_end')->nullable();

            $table->timestamps();

            $table->foreign('room_idrec')->references('idrec')->on('m_rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_rooms_door_lock');
    }
};
