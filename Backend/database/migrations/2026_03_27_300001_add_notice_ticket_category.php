<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the "Notice" ticket category for admin-initiated tickets.
 * Admins can create notice tickets to communicate with booking customers.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('t_ticket_categories')->insert([
            'ticket_type' => 'notice',
            'category' => 'notice',
            'label_en' => 'Notice',
            'label_id' => 'Pemberitahuan',
            'label_zh' => '通知',
            'recipient_type' => 'front_desk',
            'requires_booking' => true,
            'sort_order' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('t_ticket_categories')->where('category', 'notice')->delete();
    }
};
