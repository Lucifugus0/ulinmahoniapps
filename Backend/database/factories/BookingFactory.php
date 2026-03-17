<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition()
    {
        // Generate satu data transaksi
        $transaction = Transaction::factory()->create();

        return [
            'property_id' => $transaction->property_id,
            'order_id' => $transaction->order_id,
            'room_id' => $transaction->room_id,
            'check_in_at' => $transaction->check_in,
            'check_out_at' => $transaction->check_out,
            'created_by' => $transaction->user_id,
            'updated_by' => $transaction->user_id,
            'status' => $transaction->status,
        ];
    }
}
