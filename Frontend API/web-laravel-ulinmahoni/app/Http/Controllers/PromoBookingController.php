<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PromoBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Generate unique transaction code
        $transactionCode = 'TRX-' . strtoupper(Str::random(8));
        
        // Calculate booking days
        $checkIn = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $bookingDays = $checkIn->diffInDays($checkOut);
        
        // Calculate prices
        $dailyPrice = $request->discounted_price;
        $roomPrice = $dailyPrice * $bookingDays;
        $adminFees = $roomPrice * 0.1; // 10% admin fee
        $grandTotal = $roomPrice + $adminFees;

        // Create transaction
        $transaction = Transaction::create([
            'order_id' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $user->idrec,
            'user_name' => $user->name,
            'user_phone_number' => $user->phone,
            'property_name' => $request->property_name,
            'transaction_date' => now(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_name' => $request->room_name,
            'user_email' => $user->email,
            'booking_days' => $bookingDays,
            'daily_price' => $dailyPrice,
            'room_price' => $roomPrice,
            'admin_fees' => $adminFees,
            'grandtotal_price' => $grandTotal,
            'property_type' => $request->property_type,
            'transaction_type' => 'promo',
            'transaction_code' => $transactionCode,
            'transaction_status' => 'pending',
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'transaction' => $transaction
        ]);
    }
}
