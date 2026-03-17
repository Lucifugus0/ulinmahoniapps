<?php

namespace App\Http\Controllers\villa;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VillaController extends Controller {
    public function index(){
        return view("pages.villa.index");
    }

    public function show($id)
    {
        // For now, we'll use dummy data. Later you can replace this with actual database queries
        $villa = [
            'id' => $id,
            'name' => 'Xilonen Villa',
            'type' => 'Coliving',
            'location' => 'Cilandak Barat, Cilandak',
            'distance' => '976 m dari Stasiun MRT Fatmawati',
            'price' => [
                'original' => 4250000,
                'discounted' => 4025000
            ],
            'features' => [
                'Diskon sewa 12 Bulan',
                'S+ Voucher s.d. 2%'
            ],
            'image' => 'images/assets/apt.jpg',
            'attributes' => [
                'amenities' => [
                    'Private Pool',
                    'Garden',
                    'High-speed WiFi',
                    '24/7 Security',
                    'Parking Area',
                    'BBQ Area'
                ],
                'room_facilities' => [
                    'Air Conditioning',
                    'Private Bathroom',
                    'Fully Furnished',
                    'Kitchen',
                    'Living Room',
                    'Terrace'
                ],
                'rules' => [
                    'No Smoking Inside',
                    'No Loud Music After 10 PM',
                    'ID Card Required',
                    'Deposit Required'
                ]
            ],
            'description' => 'Experience the perfect blend of luxury and comfort in our spacious villa. 
                            Set in a tranquil environment, this villa offers premium amenities and modern 
                            furnishings. Ideal for those seeking a peaceful retreat while maintaining 
                            easy access to urban conveniences.'
        ];

        return view('pages.villa.show', compact('villa'));
    }
}