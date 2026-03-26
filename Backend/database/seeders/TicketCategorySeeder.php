<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the t_ticket_categories table with all predefined
 * ticket type + category combinations for the customer service system.
 */
class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            /** Booking type — routes to Front Desk, requires active booking */
            ['ticket_type' => 'booking', 'category' => 'payment', 'label_en' => 'Payment', 'label_id' => 'Pembayaran', 'label_zh' => '付款', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 1],
            ['ticket_type' => 'booking', 'category' => 'parking', 'label_en' => 'Parking', 'label_id' => 'Parkir', 'label_zh' => '停车', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 2],
            ['ticket_type' => 'booking', 'category' => 'extend_booking', 'label_en' => 'Extend Booking', 'label_id' => 'Perpanjang Booking', 'label_zh' => '延长预订', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 3],
            ['ticket_type' => 'booking', 'category' => 'others', 'label_en' => 'Others', 'label_id' => 'Lainnya', 'label_zh' => '其他', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 4],

            /** Complaint type — routes to Front Desk, requires active booking */
            ['ticket_type' => 'complaint', 'category' => 'room_building', 'label_en' => 'Room / Building', 'label_id' => 'Kamar / Gedung', 'label_zh' => '房间/建筑', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 5],
            ['ticket_type' => 'complaint', 'category' => 'loud_noise', 'label_en' => 'Loud Noise', 'label_id' => 'Kebisingan', 'label_zh' => '噪音', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 6],
            ['ticket_type' => 'complaint', 'category' => 'electricity', 'label_en' => 'Electricity', 'label_id' => 'Listrik', 'label_zh' => '电力', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 7],
            ['ticket_type' => 'complaint', 'category' => 'water', 'label_en' => 'Water', 'label_id' => 'Air', 'label_zh' => '水', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 8],
            ['ticket_type' => 'complaint', 'category' => 'wifi', 'label_en' => 'Internet / WiFi', 'label_id' => 'Internet / WiFi', 'label_zh' => '网络/WiFi', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 9],
            ['ticket_type' => 'complaint', 'category' => 'tv', 'label_en' => 'TV', 'label_id' => 'TV', 'label_zh' => '电视', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 10],
            ['ticket_type' => 'complaint', 'category' => 'others', 'label_en' => 'Others', 'label_id' => 'Lainnya', 'label_zh' => '其他', 'recipient_type' => 'front_desk', 'requires_booking' => 1, 'sort_order' => 11],

            /** Suggestion type — routes to HQ, no booking required */
            ['ticket_type' => 'suggestion', 'category' => 'suggestion_box', 'label_en' => 'Feedback & Suggestion', 'label_id' => 'Masukan & Saran', 'label_zh' => '反馈与建议', 'recipient_type' => 'hq', 'requires_booking' => 0, 'sort_order' => 12],
        ];

        foreach ($categories as $cat) {
            DB::table('t_ticket_categories')->updateOrInsert(
                ['ticket_type' => $cat['ticket_type'], 'category' => $cat['category']],
                array_merge($cat, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}
