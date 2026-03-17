<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition()
    {
        $currentYear = date('Y');
        $randomNumber = str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'property_id' => $this->faker->numberBetween(1, 7),
            'room_id' => $this->faker->numberBetween(1, 7),
            'order_id' => '#bk-' . $currentYear . '-' . $randomNumber,
            'user_id' => $this->faker->numberBetween(1, 22),
            'user_name' => $this->faker->name,
            'user_phone_number' => $this->faker->phoneNumber,
            'property_name' => $this->faker->company,
            'transaction_date' => $this->faker->dateTimeThisYear,
            'check_in' => $this->faker->dateTimeBetween('now', '+1 month'),
            'check_out' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'room_name' => $this->faker->word,
            'user_email' => $this->faker->email,
            'booking_days' => $this->faker->numberBetween(1, 30),
            'booking_months' => $this->faker->numberBetween(1, 12),
            'daily_price' => $this->faker->randomFloat(4, 50, 500),
            'room_price' => $this->faker->randomFloat(4, 500, 5000),
            'admin_fees' => $this->faker->randomFloat(4, 10, 100),
            'grandtotal_price' => $this->faker->randomFloat(4, 500, 10000),
            'property_type' => $this->faker->randomElement(['Hotel', 'Apartment', 'Villa']),
            'transaction_type' => $this->faker->randomElement(['Booking', 'Refund', 'Cancellation']),
            'transaction_code' => $this->faker->uuid,
            'transaction_status' => $this->faker->randomElement(['Pending', 'Completed', 'Failed']),
            'status' => $this->faker->randomElement([1, 0]),
            'paid_at' => $this->faker->optional()->dateTimeThisYear,
        ];
    }
}
