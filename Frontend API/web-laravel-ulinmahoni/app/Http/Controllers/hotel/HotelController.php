<?php

namespace App\Http\Controllers\hotel;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller {
    public function index(){
        return view("pages.hotel.index");
    }

    public function show($id)
    {
        // For now, we'll use dummy data. Later you can replace this with actual database queries
        $hotel = [
            'id' => $id,
            'name' => 'Kvlarya Hotel',
            'type' => 'Coliving Wanita',
            'location' => 'Tegalrejo, Bogor Utara',
            'distance' => '2.3 km dari Stasiun Bogor',
            'price' => [
                'original' => 2000000,
                'discounted' => 1891999
            ],
            'features' => [
                'Diskon sewa 12 Bulan',
                'S+ Voucher s.d. 2%'
            ],
            'image' => 'images/assets/hotel.jpg',
            'attributes' => [
                'amenities' => [
                    'High-speed WiFi',
                    '24/7 Security',
                    'Room Service',
                    'Restaurant',
                    'Fitness Center',
                    'Meeting Rooms'
                ],
                'room_facilities' => [
                    'Air Conditioning',
                    'Private Bathroom',
                    'Premium Furniture',
                    'Mini Fridge',
                    'Safe Deposit Box',
                    'Daily Housekeeping'
                ],
                'rules' => [
                    'Women Only',
                    'No Smoking',
                    'Visitor Hours 8AM-10PM',
                    'ID Card Required',
                    'Deposit Required'
                ]
            ],
            'description' => 'Welcome to Kvlarya Hotel, an exclusive women-only coliving space that combines 
                            the comfort of a hotel with the community spirit of coliving. Featuring premium 
                            amenities and a safe, supportive environment, it\'s perfect for female professionals 
                            and students looking for a sophisticated living experience.'
        ];

        return view('pages.hotel.show', compact('hotel'));
    }
} 