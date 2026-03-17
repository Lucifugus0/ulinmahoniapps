<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PropertySystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clear existing data
        DB::table('t_booking')->delete();
        DB::table('t_transactions')->delete();
        DB::table('t_payment')->delete();
        DB::table('m_rooms')->delete();
        DB::table('m_properties')->delete();

        // Seed m_properties
        $properties = [];
        $propertyNames = [
            'Jelambar House 1',
            'Jelambar House 2',
            'Ulin Mahoni House Jaksel',
            'Ulin Mahoni House Jaksel 2',
            'Jelambar House',
            'Jelambar House I',
            'Ulin Mahoni Apartment',
            'Ulin Mahoni Apartment I',
            'Ulin Mahoni Villa',
            'Ulin Mahoni Villa I'
        ];
        $tags = ['House', 'Apartment', 'Villa', 'Hotel'];

        $locations = [
            'DKI Jakarta' => ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur'],
            'Jawa Barat' => ['Bandung', 'Bekasi', 'Depok', 'Bogor', 'Cimahi'],
            'Jawa Tengah' => ['Semarang', 'Solo', 'Yogyakarta', 'Magelang', 'Salatiga'],
            'Jawa Timur' => ['Surabaya', 'Malang', 'Kediri', 'Blitar', 'Madiun'],
            'Bali' => ['Denpasar', 'Ubud', 'Sanur', 'Kuta', 'Canggu']
        ];

        $amenities = json_encode(['High-speed WiFi', 'Parking', 'Swimming Pool', 'Gym', 'Restaurant']);

        $rules = json_encode(['No Smoking', 'No Pets', 'Check-in after 2PM', 'Check-out before 12PM', 'ID Card Required', 'Deposit Required']);

        $i = 1;
        foreach ($locations as $province => $cityList) {
            $city = $cityList[array_rand($cityList)];

            $properties[$i] = [
                'idrec' => $i,
                'slug' => Str::slug($propertyNames[$i - 1]),
                'tags' => implode(',', array_slice($tags, 0, rand(1, 3))),
                'name' => $propertyNames[$i - 1],
                'initial' => substr($propertyNames[$i - 1], 0, 3),
                'description' => 'Luxurious accommodation with premium amenities and excellent service.',
                'level_count' => rand(5, 20),
                'province' => $province,
                'city' => $city,
                'subdistrict' => 'Subdistrict ' . $i,
                'village' => 'Village ' . $i,
                'postal_code' => '1000' . $i,
                'address' => 'Jl. Example No.' . $i . ', ' . $city,
                'location' => 'https://maps.google.com/?q=' . $i,
                'price' => json_encode(['daily' => rand(500000, 2000000), 'monthly' => rand(10000000, 30000000)]),
                'price_original_daily' => rand(500000, 2000000),
                'price_discounted_daily' => rand(400000, 1800000),
                'price_original_monthly' => rand(10000000, 30000000),
                'price_discounted_monthly' => rand(9000000, 28000000),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => '1',
                'updated_by' => '1'
            ];
            $i++;
        }

        DB::table('m_properties')->insert($properties);

        // Seed m_rooms
        $rooms = [];
        $roomTypes = ['Deluxe', 'Superior', 'Executive', 'Suite', 'Presidential'];
        $roomCounter = 1;

        foreach ($properties as $propertyId => $property) {
            $roomCount = rand(3, 5); // 3-5 rooms per property
            for ($j = 1; $j <= $roomCount; $j++) {
                $dailyPrice = rand(500000, 2000000);
                $monthlyPrice = rand(10000000, 30000000);

                // Pilih fasilitas random dari ["1","3","4","5"]
                $facilityOptions = ["1", "3", "4", "5"];
                $randomFacilityCount = rand(1, count($facilityOptions));
                $randomFacilities = array_rand($facilityOptions, $randomFacilityCount);
                $randomFacilities = is_array($randomFacilities)
                    ? array_map(fn($i) => $facilityOptions[$i], $randomFacilities)
                    : [$facilityOptions[$randomFacilities]];

                $rooms[] = [
                    'idrec' => $roomCounter,
                    'property_id' => $propertyId,
                    'property_name' => $property['name'],
                    'slug' => Str::slug($property['name'] . ' ' . $roomTypes[$j - 1] . ' Room'),
                    'name' => $roomTypes[$j - 1] . ' Room',
                    'descriptions' => 'Comfortable ' . $roomTypes[$j - 1] . ' room with all amenities',
                    'periode_daily' => 1,
                    'periode_monthly' => 1,
                    'bed_type' => $roomTypes[$j - 1],
                    'capacity' => rand(1, 4),
                    'size' => rand(20, 50),
                    'type' => $roomTypes[$j - 1],
                    'no' => 'R' . str_pad($roomCounter, 3, '0', STR_PAD_LEFT),
                    'level' => rand(1, $property['level_count']),
                    'facility' => json_encode($randomFacilities),
                    'price' => json_encode([
                        'original_daily' => $dailyPrice,
                        'discounted_daily' => $dailyPrice * 0.9,
                        'original_monthly' => $monthlyPrice,
                        'discounted_monthly' => $monthlyPrice * 0.85
                    ]),
                    'status' => 1,
                    'rental_status' => 0, // Default 0, akan diupdate nanti
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1
                ];

                $roomCounter++;
            }
        }

        DB::table('m_rooms')->insert($rooms);

        // Seed t_transactions, t_booking, and t_payment
        $transactions = [];
        $bookings = [];
        $payments = [];

        // Define the user IDs from AccountAdminSeeder (1-8)
        $userIds = range(1, 8);

        // Array untuk melacak room yang memiliki booking bulanan
        $monthlyBookedRooms = [];

        // ==========================
        // SPECIAL BOOKING: 3 USER MEMESAN KAMAR YANG SAMA
        // ==========================
        $specialRoomId = 1; // Pilih kamar pertama untuk booking khusus
        $specialPropertyId = 1; // Property ID untuk kamar khusus
        $specialProperty = $properties[$specialPropertyId];
        $specialRoom = $rooms[$specialRoomId - 1]; // Karena array rooms dimulai dari index 0

        // User untuk booking khusus
        $specialUsers = [
            [
                'id' => 4,
                'name' => 'Farhan',
                'email' => 'm.farhanshihab11@gmail.com',
                'booking_type' => 'daily',
                'days' => 2
            ],
            [
                'id' => 6,
                'name' => 'Hadrian',
                'email' => 'hadriannaufal10@gmail.com',
                'booking_type' => 'daily',
                'days' => 2
            ],
            [
                'id' => 8,
                'name' => 'Vincent',
                'email' => 'vincent.code7@gmail.com',
                'booking_type' => 'monthly',
                'months' => 1
            ]
        ];

        // Tanggal yang berbeda untuk setiap booking
        $baseDate = now()->addDays(5); // Mulai dari 5 hari dari sekarang
        $bookingDates = [
            $baseDate->copy(),
            $baseDate->copy()->addDays(10), // 10 hari setelah booking pertama
            $baseDate->copy()->addDays(20)  // 20 hari setelah booking pertama
        ];

        foreach ($specialUsers as $index => $user) {
            $orderId = 'SPECIAL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            $transactionDate = now();

            $price = json_decode($specialRoom['price'], true);

            // Tentukan tanggal check-in dan check-out
            $checkIn = $bookingDates[$index]->copy()->setTime(14, 0);

            if ($user['booking_type'] === 'daily') {
                $checkOut = $checkIn->copy()->addDays($user['days'])->setTime(12, 0);
                $bookingDays = $user['days'];
                $bookingMonths = null;

                $dailyPrice = $price['discounted_daily'] ?? $price['original_daily'];
                $monthlyPrice = null;
                $roomPrice = $dailyPrice * $bookingDays;
            } else {
                $checkOut = $checkIn->copy()->addMonths($user['months'])->setTime(12, 0);
                $bookingDays = null;
                $bookingMonths = $user['months'];

                $dailyPrice = null;
                $monthlyPrice = $price['discounted_monthly'] ?? $price['original_monthly'];
                $roomPrice = $monthlyPrice * $bookingMonths;

                // Tandai room ini memiliki booking bulanan
                $monthlyBookedRooms[$specialRoomId] = true;
            }

            $adminFees = $roomPrice * 0.2;
            $grandTotal = $roomPrice + $adminFees;
            $paidAt = now()->subDays(rand(0, 10));

            // ==========================
            // TRANSACTION RECORD
            // ==========================
            $transactions[] = [
                'property_id' => (string)$specialPropertyId,
                'room_id' => (string)$specialRoomId,
                'order_id' => $orderId,
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_phone_number' => '0812' . rand(1000000, 9999999),
                'property_name' => $specialProperty['name'],
                'transaction_date' => $transactionDate,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'room_name' => $specialRoom['name'],
                'user_email' => $user['email'],
                'booking_days' => $bookingDays,
                'booking_months' => $bookingMonths,

                // NEW REQUIRED FIELD
                'booking_type' => $user['booking_type'],

                // Finalized Pricing
                'daily_price' => $dailyPrice,
                'monthly_price' => $monthlyPrice,
                'room_price' => $roomPrice,
                'admin_fees' => $adminFees,
                'grandtotal_price' => $grandTotal,

                'property_type' => 'Hotel',
                'transaction_type' => 'cash',
                'transaction_code' => 'SPECIAL-' . strtoupper(Str::random(8)),
                'transaction_status' => 'paid',
                'status' => '1',
                'paid_at' => $paidAt,
                'notes' => 'Special booking - ' . $user['booking_type'] . ' period',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ==========================
            // BOOKING RECORD
            // ==========================
            $bookings[] = [
                'property_id' => $specialPropertyId,
                'order_id' => $orderId,
                'room_id' => (string)$specialRoomId,
                'check_in_at' => null,
                'check_out_at' => null,
                'created_by' => $user['id'],
                'updated_by' => $user['id'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ==========================
            // PAYMENT RECORD
            // ==========================
            $payments[] = [
                'order_id'          => $orderId,
                'user_id'           => $user['id'],
                'grandtotal_price'  => $grandTotal,
                'verified_by'       => null,
                'verified_at'       => null,
                'notes'             => "Pembayaran untuk special booking $orderId",
                'payment_status'    => 'paid',
                'created_at'        => now(),
                'updated_at'        => now(),
                'created_by'        => $user['id']
            ];
        }

        // ==========================
        // BOOKING HARI INI (TODAY BOOKING)
        // ==========================
        // Pilih kamar yang berbeda untuk booking hari ini
        $todayRoomId = 2; // Pilih kamar kedua untuk booking hari ini
        $todayPropertyId = 2; // Property ID untuk booking hari ini
        $todayProperty = $properties[$todayPropertyId];
        $todayRoom = $rooms[$todayRoomId - 1];

        // User untuk booking hari ini
        $todayUser = [
            'id' => 3,
            'name' => 'Jhon Doe',
            'email' => 'jhondoe@gmail.com',
            'booking_type' => 'daily',
            'days' => 3
        ];

        $orderId = 'TODAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $transactionDate = now();
        $price = json_decode($todayRoom['price'], true);

        // Check-in hari ini pukul 14:00
        $checkIn = now()->setTime(14, 0);
        // Check-out 3 hari dari sekarang pukul 12:00
        $checkOut = now()->addDays(3)->setTime(12, 0);
        $bookingDays = 3;

        $dailyPrice = $price['discounted_daily'] ?? $price['original_daily'];
        $roomPrice = $dailyPrice * $bookingDays;
        $adminFees = $roomPrice * 0.2;
        $grandTotal = $roomPrice + $adminFees;
        $paidAt = now()->subHours(5); // Sudah dibayar 5 jam yang lalu

        // ==========================
        // TRANSACTION RECORD untuk booking hari ini
        // ==========================
        $transactions[] = [
            'property_id' => (string)$todayPropertyId,
            'room_id' => (string)$todayRoomId,
            'order_id' => $orderId,
            'user_id' => $todayUser['id'],
            'user_name' => $todayUser['name'],
            'user_phone_number' => '0812' . rand(1000000, 9999999),
            'property_name' => $todayProperty['name'],
            'transaction_date' => $transactionDate,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_name' => $todayRoom['name'],
            'user_email' => $todayUser['email'],
            'booking_days' => $bookingDays,
            'booking_months' => null,
            'booking_type' => 'daily',
            'daily_price' => $dailyPrice,
            'monthly_price' => null,
            'room_price' => $roomPrice,
            'admin_fees' => $adminFees,
            'grandtotal_price' => $grandTotal,
            'property_type' => 'Hotel',
            'transaction_type' => 'cash',
            'transaction_code' => 'TODAY-' . strtoupper(Str::random(8)),
            'transaction_status' => 'paid',
            'status' => '1',
            'paid_at' => $paidAt,
            'notes' => 'Booking yang check-in hari ini',
            'created_at' => now()->subHours(6),
            'updated_at' => now()
        ];

        // ==========================
        // BOOKING RECORD untuk hari ini
        // ==========================
        $bookings[] = [
            'property_id' => $todayPropertyId,
            'order_id' => $orderId,
            'room_id' => (string)$todayRoomId,
            'check_in_at' => null, // Belum check-in, akan dilakukan hari ini
            'check_out_at' => null,
            'created_by' => $todayUser['id'],
            'updated_by' => $todayUser['id'],
            'status' => 1,
            'created_at' => now()->subHours(6),
            'updated_at' => now()
        ];

        // ==========================
        // PAYMENT RECORD untuk hari ini
        // ==========================
        $payments[] = [
            'order_id'          => $orderId,
            'user_id'           => $todayUser['id'],
            'grandtotal_price'  => $grandTotal,
            'verified_by'       => 1, // Admin System
            'verified_at'       => $paidAt,
            'notes'             => "Pembayaran untuk booking $orderId (check-in hari ini)",
            'payment_status'    => 'paid',
            'created_at'        => now()->subHours(6),
            'updated_at'        => now(),
            'created_by'        => $todayUser['id']
        ];

        // ==========================
        // REGULAR BOOKING (sisanya)
        // ==========================
        for ($k = 1; $k <= 6; $k++) { // Kurangi menjadi 6 karena sudah ada 3 special booking + 1 booking hari ini
            $userId = $userIds[array_rand($userIds)];
            $propertyId = rand(1, 5);

            // Hindari menggunakan kamar yang sama dengan special booking atau booking hari ini
            do {
                $property = $properties[$propertyId];
                $propertyRooms = array_filter($rooms, function ($room) use ($propertyId) {
                    return $room['property_id'] == $propertyId;
                });
                $room = $propertyRooms[array_rand($propertyRooms)];
            } while (($room['idrec'] == $specialRoomId && $propertyId == $specialPropertyId) ||
                ($room['idrec'] == $todayRoomId && $propertyId == $todayPropertyId)
            );

            $orderId = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            $transactionDate = now();

            $today = now();
            $endOfMonth = now()->endOfMonth();
            $checkIn = now()->addDays(rand(0, $today->diffInDays($endOfMonth)))->setTime(14, 0);
            $checkOut = (clone $checkIn)->addDays(rand(1, 14))->setTime(12, 0);
            $bookingDays = $checkIn->diffInDays($checkOut);

            $price = json_decode($room['price'], true);

            $hasDaily   = isset($price['discounted_daily']) || isset($price['original_daily']);
            $hasMonthly = isset($price['discounted_monthly']) || isset($price['original_monthly']);

            $dailyPrice = null;
            $monthlyPrice = null;
            $bookingTypeText = null;

            if ($hasDaily && !$hasMonthly) {
                $bookingTypeText = 'daily';
                $dailyPrice = $price['discounted_daily'] ?? $price['original_daily'];
            } elseif (!$hasDaily && $hasMonthly) {
                $bookingTypeText = 'monthly';
                $monthlyPrice = $price['discounted_monthly'] ?? $price['original_monthly'];
            } elseif ($hasDaily && $hasMonthly) {
                $pick = rand(0, 1); // random: 0=daily, 1=monthly

                if ($pick === 0) {
                    $bookingTypeText = 'daily';
                    $dailyPrice = $price['discounted_daily'] ?? $price['original_daily'];
                } else {
                    $bookingTypeText = 'monthly';
                    $monthlyPrice = $price['discounted_monthly'] ?? $price['original_monthly'];
                }
            } else {
                $bookingTypeText = 'daily';
                $dailyPrice = 0;
            }

            // Hitung harga sesuai tipe
            if ($bookingTypeText === 'daily') {
                $roomPrice = $dailyPrice * $bookingDays;
                $bookingMonths = null;
            } else {
                $months = rand(1, 3);
                $roomPrice = $monthlyPrice * $months;
                $bookingMonths = $months;

                // Tandai room ini memiliki booking bulanan
                $monthlyBookedRooms[$room['idrec']] = true;
            }

            $adminFees = $roomPrice * 0.2;
            $grandTotal = $roomPrice + $adminFees;
            $paidAt = now()->subDays(rand(0, 10));

            switch ($userId) {
                case 1:
                    $userName = 'Admin System';
                    $userEmail = 'admin_tsno@gmail.com';
                    break;
                case 2:
                    $userName = 'User Standard';
                    $userEmail = 'user_tsno@gmail.com';
                    break;
                case 3:
                    $userName = 'Jhon Doe';
                    $userEmail = 'jhondoe@gmail.com';
                    break;
                case 4:
                    $userName = 'Farhan';
                    $userEmail = 'm.farhanshihab11@gmail.com';
                    break;
                case 5:
                    $userName = 'Farhans';
                    $userEmail = 'farhans29@gmail.com';
                    break;
                case 6:
                    $userName = 'Hadrian';
                    $userEmail = 'hadriannaufal10@gmail.com';
                    break;
                case 7:
                    $userName = 'Vin User';
                    $userEmail = 'vin123@gmail.com';
                    break;
                case 8:
                    $userName = 'Vincent';
                    $userEmail = 'vincent.code7@gmail.com';
                    break;
            }

            // ==========================
            // TRANSACTION RECORD
            // ==========================
            $transactions[] = [
                'property_id' => (string)$propertyId,
                'room_id' => (string)$room['idrec'],
                'order_id' => $orderId,
                'user_id' => $userId,
                'user_name' => $userName,
                'user_phone_number' => '0812' . rand(1000000, 9999999),
                'property_name' => $property['name'],
                'transaction_date' => $transactionDate,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'room_name' => $room['name'],
                'user_email' => $userEmail,
                'booking_days' => $bookingDays,
                'booking_months' => $bookingMonths,

                // NEW REQUIRED FIELD
                'booking_type' => $bookingTypeText,

                // Finalized Pricing
                'daily_price' => $dailyPrice,
                'monthly_price' => $monthlyPrice,
                'room_price' => $roomPrice,
                'admin_fees' => $adminFees,
                'grandtotal_price' => $grandTotal,

                'property_type' => 'Hotel',
                'transaction_type' => 'cash',
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'transaction_status' => 'waiting',
                'status' => '1',
                'paid_at' => $paidAt,
                'notes' => 'Booking belum dibayar',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ==========================
            // BOOKING RECORD
            // ==========================
            $bookings[] = [
                'property_id' => $propertyId,
                'order_id' => $orderId,
                'room_id' => (string)$room['idrec'],
                'check_in_at' => null,
                'check_out_at' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ==========================
            // PAYMENT RECORD
            // ==========================
            $payments[] = [
                'order_id'          => $orderId,
                'user_id'           => $userId,
                'grandtotal_price'  => $grandTotal,
                'verified_by'       => null,
                'verified_at'       => null,
                'notes'             => "Pembayaran untuk booking $orderId",
                'payment_status'    => 'unpaid',
                'created_at'        => now(),
                'updated_at'        => now(),
                'created_by'        => $userId
            ];
        }

        DB::table('t_transactions')->insert($transactions);
        DB::table('t_booking')->insert($bookings);
        DB::table('t_payment')->insert($payments);

        // Update rental_status di m_rooms berdasarkan booking bulanan
        if (!empty($monthlyBookedRooms)) {
            $monthlyRoomIds = array_keys($monthlyBookedRooms);
            DB::table('m_rooms')
                ->whereIn('idrec', $monthlyRoomIds)
                ->update(['rental_status' => 1]);
        }
    }
}
