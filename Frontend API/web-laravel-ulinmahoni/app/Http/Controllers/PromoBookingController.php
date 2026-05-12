<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/*
 * DEAD CODE — kept only so the class file doesn't disappear from history.
 *
 * As of 2026-05-11 no route in routes/web.php or routes/api.php references
 * PromoBookingController, and no other controller / view / service mentions
 * it. The store() body below pre-dates the unified Booking flow and:
 *   - generates 'ORD-XXXXXXXX' order_ids instead of the canonical 'UMH-...'
 *     format every other entry point uses (BookingController, Api\BookingController),
 *   - inserts a t_transactions row WITHOUT original_checkin_day, which would
 *     silently break renewal arithmetic if it ever fired (see the May 2026
 *     OCD audit notes in whatsnew.md),
 *   - hard-codes a 10% admin fee that no current pricing path uses.
 *
 * If a promo-booking flow is ever revived, throw the body away and route
 * through the canonical BookingController / Api\BookingController paths so
 * order_id formatting, OCD, voucher handling, parking, and grand-total math
 * stay consistent. Do not resurrect this implementation as-is.
 */
class PromoBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        /* Body intentionally commented out — see file header. */
        // $user = Auth::user();
        //
        // // Generate unique transaction code
        // $transactionCode = 'TRX-' . strtoupper(Str::random(8));
        //
        // // Calculate booking days
        // $checkIn = \Carbon\Carbon::parse($request->check_in);
        // $checkOut = \Carbon\Carbon::parse($request->check_out);
        // $bookingDays = $checkIn->diffInDays($checkOut);
        //
        // // Calculate prices
        // $dailyPrice = $request->discounted_price;
        // $roomPrice = $dailyPrice * $bookingDays;
        // $adminFees = $roomPrice * 0.1; // 10% admin fee
        // $grandTotal = $roomPrice + $adminFees;
        //
        // // Create transaction
        // $transaction = Transaction::create([
        //     'order_id' => 'ORD-' . strtoupper(Str::random(8)),
        //     'user_id' => $user->idrec,
        //     'user_name' => $user->name,
        //     'user_phone_number' => $user->phone,
        //     'property_name' => $request->property_name,
        //     'transaction_date' => now(),
        //     'check_in' => $checkIn,
        //     'check_out' => $checkOut,
        //     'room_name' => $request->room_name,
        //     'user_email' => $user->email,
        //     'booking_days' => $bookingDays,
        //     'daily_price' => $dailyPrice,
        //     'room_price' => $roomPrice,
        //     'admin_fees' => $adminFees,
        //     'grandtotal_price' => $grandTotal,
        //     'property_type' => $request->property_type,
        //     'transaction_type' => 'promo',
        //     'transaction_code' => $transactionCode,
        //     'transaction_status' => 'pending',
        //     'status' => 'active'
        // ]);
        //
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Booking created successfully',
        //     'transaction' => $transaction
        // ]);

        return response()->json([
            'success' => false,
            'message' => 'PromoBookingController is deprecated. Use BookingController::store or Api\\BookingController::store.',
        ], 410); // 410 Gone
    }
}
