<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingFee;
use App\Models\ParkingFeeTransaction;
use App\Models\ParkingFeeTransactionImage;
use Carbon\Carbon;

class ParkingFeeTransactionSeeder extends Seeder
{
    public function run()
    {
        $parkingFees = ParkingFee::where('status', 1)->get();

        if ($parkingFees->isEmpty()) {
            $this->command->warn('No active parking fees found. Please create parking fees first.');
            return;
        }

        $statuses = ['pending', 'waiting', 'paid', 'rejected', 'canceled'];
        $names = ['Budi Santoso', 'Siti Rahma', 'Andi Wijaya', 'Dewi Lestari', 'Rizky Pratama'];
        $phones = ['081234567890', '082345678901', '083456789012', '084567890123', '085678901234'];
        $plates = ['B 1234 ABC', 'D 5678 DEF', 'L 9012 GHI', 'AB 3456 JKL', 'H 7890 MNO'];

        $now = Carbon::now();

        for ($i = 0; $i < 5; $i++) {
            $parkingFee = $parkingFees->random();
            $status = $statuses[$i];
            $txDate = $now->copy()->subDays(rand(0, 7));

            $transaction = ParkingFeeTransaction::create([
                'property_id' => $parkingFee->property_id,
                'parking_fee_id' => $parkingFee->idrec,
                'transaction_id' => null,
                'order_id' => 'PRK-' . $now->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => null,
                'user_name' => $names[$i],
                'user_phone' => $phones[$i],
                'parking_type' => $parkingFee->parking_type,
                'vehicle_plate' => $plates[$i],
                'fee_amount' => $parkingFee->fee,
                'transaction_date' => $txDate,
                'transaction_status' => $status,
                'paid_at' => in_array($status, ['paid', 'waiting']) ? $txDate->copy()->addHours(rand(1, 3)) : null,
                'verified_by' => $status === 'paid' ? 1 : null,
                'verified_at' => $status === 'paid' ? $txDate->copy()->addHours(rand(4, 6)) : null,
                'notes' => $status === 'rejected' ? 'Bukti pembayaran tidak valid' : null,
                'status' => 1,
                'created_by' => null,
            ]);

            // Add dummy payment proof image for waiting & paid transactions
            if (in_array($status, ['waiting', 'paid'])) {
                ParkingFeeTransactionImage::create([
                    'parking_transaction_id' => $transaction->idrec,
                    'image' => $this->generatePlaceholderImage(),
                    'image_type' => 'png',
                    'description' => 'Bukti transfer parking fee',
                    'status' => 1,
                    'created_by' => null,
                ]);
            }
        }

        $this->command->info('5 parking fee transactions seeded successfully.');
    }

    private function generatePlaceholderImage(): string
    {
        // Generate a small 200x100 placeholder PNG with text
        $img = imagecreatetruecolor(200, 100);
        $bg = imagecolorallocate($img, 240, 240, 240);
        $textColor = imagecolorallocate($img, 100, 100, 100);
        imagefill($img, 0, 0, $bg);
        imagestring($img, 5, 30, 40, 'Payment Proof', $textColor);

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return base64_encode($data);
    }
}
