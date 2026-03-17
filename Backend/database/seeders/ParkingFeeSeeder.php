<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingFee;
use App\Models\Property;

class ParkingFeeSeeder extends Seeder
{
    public function run()
    {
        $properties = Property::where('status', 1)->get();

        if ($properties->isEmpty()) {
            $this->command->warn('No active properties found. Please create properties first.');
            return;
        }

        $fees = [
            // [car_fee, car_capacity, motorcycle_fee, motorcycle_capacity]
            [50000, 20, 10000, 50],
            [40000, 15, 8000, 40],
            [75000, 30, 15000, 60],
            [60000, 25, 12000, 45],
            [45000, 10, 9000, 30],
        ];

        $count = 0;

        foreach ($properties as $i => $property) {
            $feeData = $fees[$i % count($fees)];

            // Car parking
            ParkingFee::updateOrCreate(
                ['property_id' => $property->idrec, 'parking_type' => 'car'],
                [
                    'fee' => $feeData[0],
                    'capacity' => $feeData[1],
                    'status' => 1,
                    'created_by' => 1,
                ]
            );
            $count++;

            // Motorcycle parking
            ParkingFee::updateOrCreate(
                ['property_id' => $property->idrec, 'parking_type' => 'motorcycle'],
                [
                    'fee' => $feeData[2],
                    'capacity' => $feeData[3],
                    'status' => 1,
                    'created_by' => 1,
                ]
            );
            $count++;
        }

        $this->command->info("{$count} parking fee records seeded successfully.");
    }
}
