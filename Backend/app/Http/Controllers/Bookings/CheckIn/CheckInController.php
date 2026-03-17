<?php

namespace App\Http\Controllers\Bookings\CheckIn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;


class CheckInController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = Booking::with(['transaction', 'property', 'room', 'user'])
            ->where('status', 1) // Only show active bookings (filter out room-changed old records)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at'); // Exclude already checked-out bookings

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Search by order_id or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Date range filter - using check_out from transaction table (only if user provides dates)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_out', [
                    $startDate,
                    Carbon::parse($endDate)->endOfDay()
                ]);
            });
        }

        $checkOuts = $query
            ->orderBy('check_in_at', 'desc')
            ->paginate($perPage);

        return view('pages.bookings.checkin.index', compact('checkOuts'));
    }


    public function filter(Request $request)
    {
        $query = Booking::with(['user', 'room', 'property', 'transaction'])
            ->where('status', 1) // Only show active bookings (filter out room-changed old records)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at'); // Exclude already checked-out bookings

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Search by order_id or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Date range filter - using check_out from transaction table (only if user provides dates)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_out', [
                    $startDate,
                    Carbon::parse($endDate)->endOfDay()
                ]);
            });
        }

        $checkOuts = $query
            ->orderBy('check_in_at', 'desc')
            ->paginate($request->input('per_page', 8));

        return response()->json([
            'table' => view('pages.bookings.checkin.partials.checkin_table', [
                'checkOuts' => $checkOuts,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $checkOuts->appends($request->input())->links()->toHtml()
        ]);
    }

    public function checkOut($order_id)
    {
        try {
            // Cari booking aktif berdasarkan order_id
            $booking = Booking::with('transaction')
                ->where('order_id', $order_id)
                ->where('status', 1) // Only get active booking
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan.'
                ], 404);
            }

            // Check if already checked out
            if ($booking->check_out_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah melakukan check-out sebelumnya.'
                ], 400);
            }

            // Check if booking is active (use getRawOriginal to bypass accessor)
            if ($booking->getRawOriginal('status') != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak aktif.'
                ], 400);
            }

            $booking->check_out_at = now();
            $booking->status = 0; // Mark booking as inactive after checkout
            $booking->save();

            // Reset rental_status on room if no other active bookings
            if ($booking->room_id) {
                $hasOtherActiveBooking = Booking::where('room_id', $booking->room_id)
                    ->where('status', 1)
                    ->where('idrec', '!=', $booking->idrec)
                    ->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid')
                          ->orWhere('transaction_status', 'waiting');
                    })
                    ->exists();

                if (!$hasOtherActiveBooking) {
                    Room::where('idrec', $booking->room_id)
                        ->update(['rental_status' => 0]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Guest successfully checked out.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBookingDetails($orderId)
    {
        $booking = Booking::with(['transaction', 'property', 'room', 'user'])
            ->where('order_id', $orderId)
            ->where('status', 1) // Get active booking only
            ->firstOrFail();

        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

        return response()->json([
            'order_id' => $booking->order_id,
            'user_name' => $transaction->user_name,
            'property_name' => $transaction->property_name,
            'room_name' => $transaction->room_name,
            'check_in' => $transaction->check_in,
            'check_out' => $transaction->check_out,
            'grandtotal_price' => $transaction->grandtotal_price,
            // Add any other fields you need from either model
            'actual_check_in' => $booking->check_in_at, // From booking model
            'actual_check_out' => $booking->check_out_at // Will be null until checked out
        ]);
    }
}
