<?php

namespace Database\Seeders;

use App\Models\PropertyFacility;
use Illuminate\Database\Seeder;

class PropertyFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            [
                'facility' => 'Swimming Pool',
                'description' => 'A refreshing pool available for residents to use during designated hours',
                'category' => 'amenities',
                'icon' => 'mdi:pool',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => '24/7 Security',
                'description' => 'Round-the-clock security personnel to ensure safety of the property',
                'category' => 'security',
                'icon' => 'mdi:security',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Parking Area',
                'description' => 'Dedicated parking spaces for residents and visitors',
                'category' => 'general',
                'icon' => 'mdi:parking',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'CCTV Surveillance',
                'description' => 'Comprehensive camera coverage for enhanced security monitoring',
                'category' => 'security',
                'icon' => 'mdi:cctv',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'WiFi Access',
                'description' => 'High-speed internet connectivity in common areas',
                'category' => 'amenities',
                'icon' => 'mdi:wifi',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Fitness Center',
                'description' => 'Well-equipped gym with modern exercise machines',
                'category' => 'amenities',
                'icon' => 'mdi:dumbbell',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Community Lounge',
                'description' => 'Shared space for social gatherings and events',
                'category' => 'general',
                'icon' => 'mdi:sofa',
                'status' => '1',
                'created_by' => '1',
            ],
            // Anda bisa menambahkan fasilitas lainnya dengan icon yang sesuai
            [
                'facility' => 'Garden Area',
                'description' => 'Beautifully maintained garden for relaxation',
                'category' => 'amenities',
                'icon' => 'mdi:flower',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Elevator',
                'description' => 'Modern elevator for easy access to all floors',
                'category' => 'general',
                'icon' => 'mdi:elevator',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Playground',
                'description' => 'Safe play area for children',
                'category' => 'amenities',
                'icon' => 'mdi:slide',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Laundry Room',
                'description' => 'Shared laundry facilities with washers and dryers',
                'category' => 'amenities',
                'icon' => 'mdi:washing-machine',
                'status' => '1',
                'created_by' => '1',
            ],
            [
                'facility' => 'Fire Safety',
                'description' => 'Fire extinguishers and smoke detectors throughout the property',
                'category' => 'security',
                'icon' => 'mdi:fire-extinguisher',
                'status' => '1',
                'created_by' => '1',
            ],
        ];

        foreach ($facilities as $facility) {
            PropertyFacility::create($facility);
        }
    }
}
