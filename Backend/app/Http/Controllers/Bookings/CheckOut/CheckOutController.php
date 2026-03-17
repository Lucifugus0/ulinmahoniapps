<?php

namespace App\Http\Controllers\Bookings\CheckOut;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomItemCondition;
use App\Models\Transaction;
use App\Models\ParkingFeeTransaction;
use App\Models\ParkingFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOutController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        // Show checked-out bookings (status=0 after checkout, check_out_at NOT NULL)
        $query = Booking::with(['transaction', 'property', 'room', 'user'])
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNotNull('check_in_at')
            ->whereNotNull('check_out_at');

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

        // Date range filter - using check_in_at from booking table (only if user provides dates)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereBetween('check_in_at', [
                $startDate,
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $bookings = $query
            // ->orderByRaw('CASE WHEN check_out_at IS NULL THEN 0 ELSE 1 END') // NULL values first
            ->orderBy('check_out_at', 'desc') // Then sort by check_out_at
            ->paginate($perPage);

        return view('pages.bookings.checkout.index', compact('bookings'));
    }

    public function filter(Request $request)
    {
        // Show checked-out bookings (status=0 after checkout, check_out_at NOT NULL)
        $query = Booking::with(['user', 'room', 'property', 'transaction'])
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNotNull('check_in_at')
            ->whereNotNull('check_out_at')
            ->orderBy('check_out_at', 'desc');

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

        // Date range filter - using check_in_at from booking table (only if user provides dates)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in_at', [
                    $startDate,
                    Carbon::parse($endDate)->endOfDay()
                ]);
            });
        }

        $bookings = $query->paginate($request->input('per_page', 8));

        return response()->json([
            'table' => view('pages.bookings.checkout.partials.checkout_table', [
                'bookings' => $bookings,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $bookings->appends($request->input())->links()->toHtml()
        ]);
    }

    public function checkOut(Request $request, $order_id)
    {
        try {
            DB::beginTransaction();

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

            // Checkout selalu membebaskan kamar (rental_status = 0)
            if ($booking->room_id) {
                Room::where('idrec', $booking->room_id)
                    ->update(['rental_status' => 0]);
            }

            // Simpan kondisi barang
            $itemConditions = $request->input('item_conditions', []);
            $additionalNotes = $request->input('additional_notes', '');
            $damageCharges = $request->input('damage_charges', 0);

            foreach ($itemConditions as $item) {
                RoomItemCondition::create([
                    'order_id' => $order_id,
                    'booking_id' => $booking->idrec,
                    'item_name' => $item['name'],
                    'condition' => $item['condition'],
                    'custom_text' => $item['customText'] ?? null,
                    'notes' => $additionalNotes,
                    'damage_charge' => $item['name'] === 'Total' ? $damageCharges : 0,
                    'created_by' => Auth::id(),
                ]);
            }

            // Release parking quota for this order_id
            $this->releaseParkingQuota($order_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guest successfully checked out.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

    /**
     * Release parking quota for the given order_id.
     * Mencakup semua sumber parkir:
     *   1. t_parking_fee_transaction (Parking Payment)
     *   2. t_transactions (parkir bundled dengan booking kamar)
     *   3. t_parking management_only=1 (Parking Management)
     */
    private function releaseParkingQuota($order_id)
    {
        try {
            $txn        = Transaction::where('order_id', $order_id)->first();
            $propertyId = $txn->property_id ?? null;
            $userId     = $txn->user_id ?? null;

            // Track tipe yang sudah dibebaskan agar tidak double decrement
            $releasedTypes = [];

            // === Sumber 1: t_parking_fee_transaction (Alur Parking Payment) ===
            $parkingTransactions = ParkingFeeTransaction::where('order_id', $order_id)
                ->where('transaction_status', 'paid')
                ->where('status', 1)
                ->get();

            foreach ($parkingTransactions as $pt) {
                $pt->update(['transaction_status' => 'completed']);

                // Cari ParkingFee via parking_fee_id atau fallback ke property+type
                $parkingFee = $pt->parking_fee_id
                    ? ParkingFee::find($pt->parking_fee_id)
                    : ParkingFee::where('property_id', $pt->property_id)
                        ->where('parking_type', $pt->parking_type)
                        ->where('status', 1)
                        ->first();

                if ($parkingFee && $parkingFee->capacity > 0) {
                    $parkingFee->decrementQuota(1);
                    $releasedTypes[] = $pt->parking_type;
                    Log::info("Parking quota released (PP) order {$order_id}, type: {$pt->parking_type}");
                }
            }

            // === Sumber 2: t_transactions — parkir bundled dengan booking kamar ===
            if ($txn && $txn->parking_type && $txn->parking_fee > 0 && $propertyId) {
                if (!in_array($txn->parking_type, $releasedTypes)) {
                    $parkingFee = ParkingFee::where('property_id', $propertyId)
                        ->where('parking_type', $txn->parking_type)
                        ->where('status', 1)
                        ->first();

                    if ($parkingFee && $parkingFee->capacity > 0) {
                        $parkingFee->decrementQuota(1);
                        $releasedTypes[] = $txn->parking_type;
                        Log::info("Parking quota released (txn) order {$order_id}, type: {$txn->parking_type}");
                    }
                }
            }

            // === Sumber 3: t_parking management_only=1 (Alur Parking Management) ===
            $parkingRecords = \App\Models\Parking::where('order_id', $order_id)
                ->where('management_only', 1)
                ->where('status', 1)
                ->get();

            foreach ($parkingRecords as $pr) {
                if ($pr->property_id && !in_array($pr->parking_type, $releasedTypes)) {
                    $parkingFee = ParkingFee::where('property_id', $pr->property_id)
                        ->where('parking_type', $pr->parking_type)
                        ->where('status', 1)
                        ->first();

                    if ($parkingFee && $parkingFee->capacity > 0) {
                        $parkingFee->decrementQuota(1);
                        $releasedTypes[] = $pr->parking_type;
                        Log::info("Parking quota released (PM) order {$order_id}, type: {$pr->parking_type}");
                    }
                }

                // Soft-delete parking record dari Parking Management
                $pr->update(['status' => 0, 'updated_by' => Auth::id()]);
                $pr->delete();
            }
        } catch (\Exception $e) {
            Log::error("Failed to release parking quota for order {$order_id}: " . $e->getMessage());
            // Jangan throw — parking release tidak boleh menghalangi proses checkout
        }
    }
}
