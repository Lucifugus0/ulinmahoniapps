<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create the m_room_name_types master table — global room type names
     * (e.g., Standar, Superior, Deluxe, Suite) that admins can add to.
     */
    public function up(): void
    {
        Schema::create('m_room_name_types', function (Blueprint $table) {
            $table->increments('idrec');
            $table->string('name', 100);
            $table->string('status')->default('1');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        // Seed default room types
        $defaults = ['Standar', 'Superior', 'Deluxe', 'Suite'];
        foreach ($defaults as $type) {
            DB::table('m_room_name_types')->insert([
                'name' => $type,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('m_room_name_types');
    }
};
