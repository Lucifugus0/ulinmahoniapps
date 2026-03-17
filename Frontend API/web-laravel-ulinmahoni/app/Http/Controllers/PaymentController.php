<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show($order_id)
    {
        $booking = Transaction::where('order_id', $order_id)->firstOrFail();

        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load('room');

        return view('pages.payment.show', compact('booking'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'payment_method' => 'required|in:bca,mandiri,bni'
        ]);

        $booking = Transaction::where('order_id', $request->order_id)->firstOrFail();

        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Here you would integrate with your payment gateway
        // For now, we'll just simulate a successful payment
        DB::transaction(function() use ($booking) {
            $booking->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);
        });

        return response()->json([
            'success' => true,
            'redirect_url' => route('bookings.show', $booking)
        ]);
    }
}
