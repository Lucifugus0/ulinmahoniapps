<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\ApiController;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\VoucherService;
use App\Jobs\ExpireBooking;
use App\Notifications\BookingConfirmationNotification;
use App\Models\User;
use App\Services\FirebaseNotificationService;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Refund;
use App\Services\RefundCalculationService;

class BookingController extends ApiController
{
    /**
     * Check and expire pending bookings that have passed their expiration time
     * This runs automatically when users access their bookings
     *
     * @return void
     */
    private function checkAndExpireBookings()
    {
        try {
            $now = now();

            // Find all pending transactions that are past their expiration time
            $expiredTransactions = Transaction::where('transaction_status', 'pending')
                ->where('expired_at', '<=', $now)
                ->whereNotNull('expired_at')
                ->get();

            if ($expiredTransactions->isEmpty()) {
                return;
            }

            foreach ($expiredTransactions as $transaction) {
                try {
                    DB::beginTransaction();

                    // Double check payment status
                    $payment = Payment::where('order_id', $transaction->order_id)->first();
                    if ($payment && $payment->payment_status === 'paid') {
                        Log::info("Skipping expiration - Payment already completed for order_id: {$transaction->order_id}");
                        DB::rollBack();
                        continue;
                    }

                    // Update transaction status to expired
                    $transaction->update([
                        'transaction_status' => 'expired',
                        'status' => '0', // Inactive
                    ]);

                    // Update payment status if exists
                    if ($payment) {
                        $payment->update([
                            'payment_status' => 'expired'
                        ]);
                    }

                    // Update booking status if exists
                    $booking = Booking::where('order_id', $transaction->order_id)->first();
                    if ($booking) {
                        $booking->update([
                            'status' => '0' // Inactive
                        ]);
                    }

                    // Restore voucher usage count if voucher was used
                    if ($transaction->voucher_id) {
                        $voucher = \App\Models\Voucher::find($transaction->voucher_id);
                        if ($voucher && $voucher->current_usage_count > 0) {
                            $voucher->decrement('current_usage_count');
                            Log::info("Restored voucher usage count for voucher_id: {$transaction->voucher_id}");
                        }
                    }

                    DB::commit();
                    Log::info("Auto-expired booking on API access for order_id: {$transaction->order_id}");

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to auto-expire booking for order_id: {$transaction->order_id}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error in checkAndExpireBookings (API)", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Increment parking quota when a booking uses parking
     *
     * @param int $propertyId
     * @param string|null $parkingType
     * @return void
     */
    private function incrementParkingQuota($propertyId, $parkingType)
    {
        if (empty($parkingType)) {
            return;
        }

        $parkingFee = \App\Models\ParkingFee::where('property_id', $propertyId)
            ->where('parking_type', $parkingType)
            ->where('status', '1')
            ->first();

        if ($parkingFee) {
            $parkingFee->increment('quota_used');
            Log::info("Incremented parking quota", [
                'property_id' => $propertyId,
                'parking_type' => $parkingType,
                'new_quota_used' => $parkingFee->quota_used
            ]);
        }
    }

    private function decrementParkingQuota($propertyId, $parkingType)
    {
        if (empty($parkingType)) {
            return;
        }

        $parkingFee = \App\Models\ParkingFee::where('property_id', $propertyId)
            ->where('parking_type', $parkingType)
            ->where('status', '1')
            ->first();

        if ($parkingFee && $parkingFee->quota_used > 0) {
            $parkingFee->decrement('quota_used');
        }
    }

    public function index(Request $request)
    {
        // Auto-expire pending bookings when user accesses their bookings
        // $this->checkAndExpireBookings();

        try {
            $query = Transaction::query();

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('transaction_status')) {
                $query->where('transaction_status', $request->transaction_status);
            }

            $query->orderBy('created_at', 'desc');

            // Helper function to add room_no and check_in_at to each booking
            $addRoomNo = function ($bookings) {
                return collect($bookings)->map(function ($booking) {
                    $bookingData = $booking->toArray();
                    $bookingData['check_in_at'] = $booking->getCheckInAt();

                    // Get room no from m_rooms table
                    if ($booking->room_id) {
                        $room = DB::table('m_rooms')->where('idrec', $booking->room_id)->first();
                        $bookingData['room_no'] = $room?->no;
                    } else {
                        $bookingData['room_no'] = null;
                    }

                    return $bookingData;
                })->all();
            };

            if ($request->has('limit') && $request->has('page')) {
                $transactions = $query->paginate($request->limit);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Bookings retrieved successfully',
                    'data' => $addRoomNo($transactions->items()),
                    'meta' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total()
                    ]
                ]);
            }

            $transactions = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Bookings retrieved successfully',
                'data' => $addRoomNo($transactions)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkInByOrderId(Request $request, $order_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'doc_type' => 'required|string',
                'document' => 'required|string', // base64 encoded image
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $orderId = $order_id;

            // Find the booking by order_id
            $booking = Booking::where('order_id', $orderId)
                ->where('status', '1')
                ->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found or already inactive'
                ], 404);
            }

            // Check if already checked in (validate if check_in_at and doc_path are already filled)
            if (!is_null($booking->check_in_at) && !is_null($booking->doc_path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking has already been checked in. Cannot perform check-in again.',
                    'data' => [
                        'order_id' => $orderId,
                        'check_in_at' => $booking->check_in_at,
                        'doc_type' => $booking->doc_type,
                        'doc_path' => $booking->doc_path
                    ]
                ], 400);
            }

            // Process base64 document upload
            $base64Image = $request->input('document');

            // Remove data URL prefix if present
            if (strpos($base64Image, ';base64,') !== false) {
                [$_, $base64Image] = explode(';', $base64Image);
                [$_, $base64Image] = explode(',', $base64Image);
            }

            // Validate base64 image
            $imageData = base64_decode($base64Image, true);
            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid base64 image'
                ], 422);
            }

            // Validate image type
            $f = finfo_open();
            $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
            finfo_close($f);

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!in_array($mimeType, $allowedTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid image type. Only JPEG, PNG, and GIF are allowed.'
                ], 422);
            }

            // Determine file extension from mime type
            $extensionMap = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/jpg' => 'jpg',
                'image/gif' => 'gif'
            ];
            $extension = $extensionMap[$mimeType] ?? 'jpg';

            // Generate filename and store the image
            $filename = 'doc_' . $orderId . '_' . time() . '.' . $extension;
            $path = 'documents/' . $filename;

            // Save file to storage/app/public/documents
            Storage::disk('public')->put($path, $imageData);

            // Update booking with check-in data
            $booking->check_in_at = Carbon::now();
            $booking->doc_type = $request->doc_type;
            $booking->doc_path = $path;
            $booking->save();

            // Also update transaction status if needed
            // Transaction::where('order_id', $orderId)
            //     ->where('status', '1')
            //     ->update([
            //         'transaction_status' => 'checked_in',
            //         'updated_at' => Carbon::now()
            //     ]);

            // Send push notification for check-in
            try {
                $fcm = new FirebaseNotificationService();
                $transaction = Transaction::where('order_id', $orderId)->first();
                $propertyName = $transaction->property_name ?? 'property';

                // Notify guest
                if ($transaction && $transaction->user_id) {
                    $guest = \App\Models\User::find($transaction->user_id);
                    if ($guest) {
                        $fcm->sendToUser($guest, 'Check-In Successful', "Welcome to {$propertyName}!", [
                            'type' => 'check_in',
                            'order_id' => $orderId,
                        ]);
                    }
                }

                // Notify admins
                $fcm->sendToAdmins('Guest Check-In', "{$transaction->user_name} checked in at {$propertyName}.", [
                    'type' => 'check_in',
                    'order_id' => $orderId,
                ]);
            } catch (\Exception $pushError) {
                Log::warning('Push notification failed (check-in): ' . $pushError->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Check-in successful',
                'data' => [
                    'order_id' => $orderId,
                    'check_in_at' => $booking->check_in_at,
                    'doc_type' => $booking->doc_type,
                    'doc_path' => $path,
                    'doc_url' => Storage::disk('public')->url($path)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error during check-in',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function checkAvailability(Request $request)
    {
        // Booking-type-aware date constraints:
        //   New booking (is_renewal=0): check-in cap by type — 90d daily / 14d monthly.
        //   Renewal (is_renewal=1):     no check-in cap. Daily renewal: check_out ≤ today+60d.
        //                               Monthly renewal: no date cap at all.
        $isRenewal   = $request->is_renewal == 1;
        $bookingType = $request->booking_type ?? 'daily';

        $checkInRules  = ['required', 'date', 'after_or_equal:today'];
        $checkOutRules = ['required', 'date', 'after:check_in'];

        if ($isRenewal) {
            // Renewal: cap check_out at today+60d only for daily.
            if ($bookingType === 'daily') {
                $checkOutRules[] = 'before_or_equal:' . now()->addDays(60)->format('Y-m-d');
            }
        } else {
            // New booking: cap check_in by type.
            $checkInRules[] = $bookingType === 'monthly'
                ? 'before_or_equal:' . now()->addDays(14)->format('Y-m-d')
                : 'before_or_equal:' . now()->addDays(90)->format('Y-m-d');
        }

        $validator = \Validator::make($request->all(), [
            'property_id'  => 'required|integer|exists:m_properties,idrec',
            'room_id'      => 'required|integer|exists:m_rooms,idrec',
            'check_in'     => $checkInRules,
            'check_out'    => $checkOutRules,
            'is_renewal'   => 'nullable|integer|in:0,1',
            'booking_type' => 'nullable|string|in:daily,monthly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $propertyId = $request->property_id;
        $roomId = $request->room_id;
        // Use actual booking times (14:00 check-in, 12:00 check-out) instead of startOfDay/endOfDay
        // Allows back-to-back daily bookings on same day (checkout noon, checkin 2PM)
        $checkIn = Carbon::parse($request->check_in . ' 14:00:00');
        $checkOut = Carbon::parse($request->check_out . ' 12:00:00');
        $isRenewal = $request->is_renewal == 1;

        // Check room availability based on rental type:
        // Daily rooms (periode_daily=1) are always available.
        // Monthly-only rooms check active bookings for date overlap.
        $room = DB::table('m_rooms')->where('idrec', $roomId)->first();
        if (!$isRenewal && $room && !$room->periode_daily) {
            // Monthly-only room: check if there's a conflicting active booking
            $hasConflict = DB::table('t_booking')
                ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
                ->where('t_booking.room_id', $roomId)
                ->where('t_booking.status', 1)
                ->whereNull('t_booking.check_out_at')
                ->whereNotIn('t_transactions.transaction_status', ['cancelled', 'expired', 'checked_out', 'rejected'])
                ->where('t_transactions.check_in', '<', $checkOut)
                ->where('t_transactions.check_out', '>', $checkIn)
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'is_available' => false,
                        'reason' => 'Room is currently rented',
                        'conflicting_bookings' => [],
                        'check_in' => $checkIn->format('Y-m-d H:i:s'),
                        'check_out' => $checkOut->format('Y-m-d H:i:s'),
                        'property_id' => $propertyId,
                        'room_id' => $roomId,
                        'user_info' => null,
                    ]
                ]);
            }
        }

        // Check for conflicting bookings using simplified query
        
        // $conflictingBookings = DB::table('t_transactions')
        //     ->where('property_id', $propertyId) //property_id from the m_properties
        //     ->where('room_id', $roomId) //room_id from the m_rooms
        //     ->where('status', '1')  // if the status = 1
        //     ->whereNotIn('transaction_status', ['cancelled','expired','checked_out']) // if the transaction_status is not cancelled, finished, completed, paid
        //     ->where('check_in', '<', $checkOut) // if the check_in is less than the check_out
        //     ->where('check_out', '>', $checkIn) // if the check_out is greater than the check_in
        //     ->limit(5) // limit the result to 5
        //     ->get();

        
        // Query from t_booking as source of truth for room assignments.
        // When a booking is reassigned to a new room, original booking status → 0, new row status → 1.
        // Join t_transactions only for scheduled dates and payment status.
        $conflictingBookings = DB::table('t_booking')
            ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->where('t_booking.property_id', $propertyId)
            ->where('t_booking.room_id', $roomId)
            ->where('t_booking.status', 1)
            ->whereNull('t_booking.check_out_at')
            ->whereNotIn('t_transactions.transaction_status', [
                'cancelled',
                'expired',
                'checked_out',
                'rejected',
            ])
            ->where('t_transactions.check_in', '<', $checkOut)
            ->where('t_transactions.check_out', '>', $checkIn)
            ->select(
                't_transactions.*',
                't_booking.check_in_at',
                't_booking.check_out_at',
                't_booking.room_id as booking_room_id'
            )
            ->limit(5)
            ->get();

        $isAvailable = $conflictingBookings->isEmpty();

        // Get the first booking if available
        $firstBooking = $conflictingBookings->first();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'is_available' => $isAvailable,
                'conflicting_bookings' => $isAvailable ? [] : $conflictingBookings,
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'property_id' => $propertyId,
                'room_id' => $roomId,
                'user_info' => $firstBooking ? [
                    'user_name' => $firstBooking->user_name ?? null,
                    'user_phone_number' => $firstBooking->user_phone_number ?? null,
                    'user_email' => $firstBooking->user_email ?? null,
                ] : null,
            ]
        ]);
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }

            $data = $transaction->toArray();
            $data['check_in_at'] = $transaction->getCheckInAt();

            // Get room no from m_rooms table
            if ($transaction->room_id) {
                $room = DB::table('m_rooms')->where('idrec', $transaction->room_id)->first();
                $data['room_no'] = $room ? $room->no : null;
            } else {
                $data['room_no'] = null;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Booking retrieved successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'user_name' => 'required|string|max:255',
            'user_phone_number' => 'nullable|string|max:20',
            'user_email' => 'required|email|max:255',
            'property_id' => 'nullable|integer',
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string',
            'room_name' => 'required|string|max:255',
            'room_id' => 'nullable|integer',
            'booking_type' => 'nullable',
            // Daily: check-in max 90 days from now. Monthly: max 14 days from now.
            'check_in' => [
                'required', 'date', 'after_or_equal:today',
                'before_or_equal:' . ($request->booking_months ? now()->addDays(14)->format('Y-m-d') : now()->addDays(90)->format('Y-m-d')),
            ],
            'check_out' => 'required|date|after:check_in',
            'daily_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'booking_days' => 'nullable|integer|required_without:booking_months|max:60',
            'booking_months' => 'nullable|integer|required_without:booking_days|max:12',
            'voucher_code' => 'nullable|string|min:8|max:20',
            // DEPOSIT & PARKING
            'deposit_fee' => 'nullable|numeric|min:0',
            'parking_type' => 'nullable|string',
            'parking_fee' => 'nullable|numeric|min:0',
            'parking_duration' => 'nullable|integer|min:0'
        ], [
            'booking_days.required_without' => 'Either booking_days or booking_months is required',
            'booking_months.required_without' => 'Either booking_days or booking_months is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Block deprecated BRI Manual payment method (old mobile app versions)
        if ($request->transaction_type && stripos($request->transaction_type, 'bri') !== false && stripos($request->transaction_type, 'manual') !== false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Metode pembayaran Transfer BRI Manual sudah tidak tersedia. Silakan update aplikasi Anda ke versi terbaru untuk menggunakan metode pembayaran lainnya.',
                'error_code' => 'PAYMENT_METHOD_DEPRECATED'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $bookingDays = null;
            $bookingMonths = null;
            $roomPrice = 0;
            $adminFees = 0;
            $serviceFees = 30000;
            $grandtotalPrice = 0;
            // Set check-in time to 14:00:00 and check-out time to 12:00:00
            // Use createFromFormat to avoid timezone issues with date-only strings
            $checkIn = Carbon::createFromFormat('Y-m-d', $request->check_in, config('app.timezone'))->setTime(14, 0, 0);
            $checkOut = Carbon::createFromFormat('Y-m-d', $request->check_out, config('app.timezone'))->setTime(12, 0, 0);

            // Determine booking type - prioritize explicit booking_type parameter
            $isDaily = $request->booking_type === 'daily' ||
                       ($request->booking_type !== 'monthly' && $request->has('booking_days') && $request->booking_days > 0);

            if ($isDaily) {
                // DAILY BOOKING
                // Calculate days using start of day to count full days/nights
                $calculatedDays = $checkIn->copy()->startOfDay()->diffInDays($checkOut->copy()->startOfDay());

                // Verify the calculated days match the provided booking_days
                // if ($calculatedDays != $request->booking_days) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'The check-in/check-out dates do not match the provided booking days',
                //         'calculated_days' => $calculatedDays
                //     ], 422);
                // }

                $bookingDays = $calculatedDays;
                $roomPrice = $request->daily_price * $bookingDays;
                // $adminFees = $roomPrice * 0.10;
                $adminFees = $request->admin_fees;
                $serviceFees = $request->service_fees;
                $tax = $request->tax;
                // Calculate subtotal before service fees (for voucher calculation)
                $subtotalBeforeServiceFee = $roomPrice + $adminFees + $tax;
            } else {
                // MONTHLY BOOKING
                $monthlyPrice = $request->monthly_price;
                $bookingMonths = $request->booking_months;

                // Defensive server-side checkout clamp for monthly bookings.
                // Ignore the client-submitted check_out and recompute it from
                // check_in + booking_months, clamping the day-of-month to the
                // target month's last day. This prevents overflow bugs from
                // legacy/old clients (e.g. Mar 31 + 1 month producing May 1
                // instead of Apr 30) which then cascade into renewal chains.
                $targetMonth = $checkIn->month + $bookingMonths;
                $targetYear = $checkIn->year + intdiv($targetMonth - 1, 12);
                $targetMonth = (($targetMonth - 1) % 12) + 1;
                $maxDay = Carbon::create($targetYear, $targetMonth, 1)->daysInMonth;
                $clampedDay = min($checkIn->day, $maxDay);
                $checkOut = Carbon::create($targetYear, $targetMonth, $clampedDay, 12, 0, 0, config('app.timezone'));

                $roomPrice = $monthlyPrice * $bookingMonths;
                // $adminFees = $roomPrice * 0.10;
                $adminFees = $request->admin_fees;
                $serviceFees = $request->service_fees;
                $tax = $request->tax;
                // Calculate subtotal before service fees (for voucher calculation)
                $subtotalBeforeServiceFee = $roomPrice + $adminFees + $tax;
            }

            // Voucher processing
            // Formula: Grandtotal = Subtotal - Voucher + Service Fee
            $voucherId = null;
            $voucherCode = null;
            $discountAmount = 0;
            $subtotalBeforeDiscount = $subtotalBeforeServiceFee;

            if ($request->filled('voucher_code')) {
                $voucherService = app(VoucherService::class);

                // Validate and apply voucher (applied to subtotal before service fee)
                $voucherValidation = $voucherService->validateVoucher(
                    $request->voucher_code,
                    $request->user_id,
                    $subtotalBeforeServiceFee,
                    $request->property_id,
                    $request->room_id
                );

                if (!$voucherValidation['valid']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Voucher validation failed',
                        'errors' => $voucherValidation['errors']
                    ], 422);
                }

                $voucher = $voucherValidation['voucher'];
                $calculation = $voucherService->calculateDiscount($voucher, $subtotalBeforeServiceFee);

                $voucherId = $voucher->idrec;
                $voucherCode = $voucher->code;
                $discountAmount = $calculation['discount_amount'];

                // Increment voucher usage count
                $voucher->increment('current_usage_count');
            }

            // Handle deposit fee
            $depositFee = floatval($request->deposit_fee ?? 0);

            // Handle parking fee - store as is (no calculation with duration)
            $parkingDuration = intval($request->parking_duration ?? 0);
            $parkingFee = floatval($request->parking_fee ?? 0);
            $parkingType = $request->parking_type ?? null;

            // Calculate final grand total: Subtotal - Discount + Service Fee + Deposit + Parking
            $grandtotalPrice = $subtotalBeforeServiceFee - $discountAmount + $serviceFees + $depositFee + $parkingFee;

            // Generate order_id in format INV-UM-APP-yymmddXXXPP
            $property = $request->property_id ? Property::find($request->property_id) : null;
            $propertyInitial = $property && $property->initial ? $property->initial : 'XX';
            do {
                $randomNumber = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
                $order_id = 'UMH-' . now()->format('ymd') . $randomNumber . $propertyInitial;
            } while (Transaction::where('order_id', $order_id)->exists());

            // Set expiration time to 30 minutes from now
            $expiredAt = now()->addMinutes(30);

            // Prepare transaction data
            $transactionData = [
                // USER DATAS
                'user_id' => $request->user_id,
                'user_name' => $request->user_name,
                'user_phone_number' => $request->user_phone_number,
                'user_email' => $request->user_email,
                // PROPERTY DATAS
                'property_id' => $request->property_id,
                'property_name' => $request->property_name,
                'property_type' => $request->property_type,
                // ROOM DATAS
                'room_id' => $request->room_id,
                'room_name' => $request->room_name,
                // ORDER DETAILS
                'order_id' => $order_id,
                'transaction_date' => now(),
                'booking_type' => $isDaily ? 'daily' : 'monthly',
                'booking_days' => $bookingDays,
                'booking_months' => $bookingMonths,
                // PRICES
                'room_price' => $roomPrice,
                'daily_price' => $request->daily_price,
                'monthly_price' => $request->monthly_price,
                'admin_fees' => $adminFees,
                'service_fees' => $request->service_fees,
                'grandtotal_price' => $grandtotalPrice,
                // VOUCHER DATA
                'voucher_id' => $voucherId,
                'voucher_code' => $voucherCode,
                'discount_amount' => $discountAmount,
                'subtotal_before_discount' => $subtotalBeforeDiscount,
                // DEPOSIT & PARKING
                'deposit_fee' => $depositFee,
                'parking_type' => $parkingType,
                'parking_fee' => $parkingFee,
                'parking_duration' => $parkingDuration,
                // CODE AND STATUS
                'transaction_type' => $request->transaction_type,
                'transaction_code' => 'TRX-' . Str::random(16),
                'transaction_status' => 'pending',
                'status' => '1',
                // DATES
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                // Preserve the day-of-month from check-in for renewal checkout calculation
                // e.g. Jan 31 check-in → original_checkin_day = 31
                'original_checkin_day' => Carbon::parse($checkIn)->day,
                'expired_at' => $expiredAt,
            ];

            // Create transaction
            $transaction = Transaction::create($transactionData);

            // Log voucher usage if voucher was applied
            if ($voucherId && $voucherCode) {
                $voucherService = app(VoucherService::class);
                $voucherService->logUsage([
                    'voucher_id' => $voucherId,
                    'voucher_code' => $voucherCode,
                    'user_id' => $request->user_id,
                    'order_id' => $order_id,
                    'transaction_id' => $transaction->idrec,
                    'property_id' => $request->property_id,
                    'room_id' => $request->room_id,
                    'original_amount' => $subtotalBeforeDiscount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $grandtotalPrice
                ]);
            }

            // Create booking if room_id is provided
            if ($request->has('room_id') && $request->room_id) {
                $bookingData = [
                    'property_id' => $request->property_id,
                    'order_id' => $order_id,
                    'room_id' => $request->room_id,
                    // 'check_in_at' => $request->check_in,
                    // 'check_out_at' => $request->check_out,
                    'status' => '1',
                    'booking_type' => $request->has('booking_days') && $request->booking_days > 0 ? 'daily' : 'monthly'
                ];
                Booking::create($bookingData);
            }

            // Create payment record
            $paymentData = [
                'property_id' => $request->property_id,
                'room_id' => $request->room_id,
                'order_id' => $order_id,
                'user_id' => $request->user_id,
                'grandtotal_price' => $grandtotalPrice,
                'payment_status' => 'unpaid',
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Payment::create($paymentData);

            // Booking will be automatically expired by scheduled task if not paid within 1 hour
            Log::info("Booking created with expiration time: {$expiredAt} for order_id: {$order_id}");

            /* m_rooms.rental_status is NOT touched on booking creation. That
               flag tracks physical occupancy (guest is in the room) — only
               check-in sets it to 1, only check-out sets it to 0. */

            // Increment parking quota if parking is used (only for new bookings, not renewals)
            if ($request->is_renewal != 1 && $parkingFee > 0 && $parkingType) {
                $this->incrementParkingQuota($request->property_id, $parkingType);
            }

            // Process payment with DOKU
            // $dokuPaymentResponse = $this->processDokuPayment([
            //     'order_id' => $order_id,
            //     'transaction_code' => $transaction->transaction_code,
            //     'amount' => $grandtotalPrice,
            //     'property_name' => $request->property_name,
            //     'room_name' => $request->room_name,
            //     'user_name' => $request->user_name,
            //     'user_email' => $request->user_email,
            //     'user_phone' => $request->user_phone_number
            // ]);

            if (isset($dokuPaymentResponse['error'])) {
                $errorMessage = 'Doku API Error: ' . json_encode($dokuPaymentResponse['error'] ?? [], JSON_PRETTY_PRINT) . ' - ' . json_encode($dokuPaymentResponse['error'] ?? [], JSON_PRETTY_PRINT);
                \Log::error($errorMessage);
                throw new \Exception($errorMessage);
            }

            // Update transaction with payment URL (expired_at already set during create)
            $transaction->update([
                'payment_url' => $dokuPaymentResponse['payment_url'] ?? null,
            ]);

           // Send booking confirmation email
            if ($request->user_email) {
                // Create a temporary user model with the email
                $user = new \App\Models\User();
                $user->email = $request->user_email;
                $user->id = $request->user_id ?? 0; // Use provided user_id or default to 0 for guests

                // Prepare booking data
                $bookingData = [
                    'property_id' => $request->property_id,
                    'order_id' => $order_id,
                    'room_id' => $request->room_id,
                    'status' => '1',
                    'booking_type' => $request->has('booking_days') && $request->booking_days > 0 ? 'daily' : 'monthly'
                ];

                // Send email using the centralized method
                $this->sendEmailBooking(
                    $user,
                    $bookingData,
                    $transactionData,
                    $dokuPaymentResponse['payment_url'] ?? null
                );
            }

            // Send booking confirmation email
            // if ($request->user_email) {
            //     $this->sendEmailBooking($request->user_email, [
            //         'property_id' => $request->property_id,
            //         'order_id' => $order_id,
            //         'room_id' => $request->room_id,
            //         'status' => '1',
            //         'booking_type' => $request->has('booking_days') && $request->booking_days > 0 ? 'daily' : 'monthly'
            //     ], $transactionData, $dokuPaymentResponse['payment_url'] ?? null);
            // }

            DB::commit();

            // Send push notifications for new booking
            try {
                $fcm = new FirebaseNotificationService();
                $propertyName = $request->property_name;

                // Notify guest
                $guest = \App\Models\User::find($request->user_id);
                if ($guest) {
                    $fcm->sendToUser($guest, 'Booking Created', "Your booking for {$propertyName} has been created. Complete payment before it expires.", [
                        'type' => 'booking_created',
                        'order_id' => $order_id,
                    ]);
                }

                // Notify admins
                $fcm->sendToAdmins('New Booking', "{$request->user_name} booked {$propertyName} ({$request->room_name}). Order: {$order_id}.", [
                    'type' => 'booking_created',
                    'order_id' => $order_id,
                ]);
            } catch (\Exception $pushError) {
                \Log::warning('Push notification failed (booking created): ' . $pushError->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully',
                'data' => array_merge($transaction->toArray(), [
                    'payment_url' => $dokuPaymentResponse['payment_url'] ?? null,
                    'expired_at' => $expiredAt->toISOString()
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating booking',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function renewBooking(Request $request, $orderId)
    {
        // Daily renewal: check_out capped at today+60d. Monthly renewal: no date cap.
        // (Monthly's separate 15-month cumulative cap is enforced below after chain-walking.)
        $checkOutRules = ['required', 'date', 'after:check_in'];
        if ($request->booking_type === 'daily') {
            $checkOutRules[] = 'before_or_equal:' . now()->addDays(60)->format('Y-m-d');
        }

        // Validate request - similar to store booking but with is_renewal
        $validator = Validator::make($request->all(), [
            // USER INFO
            'user_id' => 'required|integer',
            'user_name' => 'required|string|max:255',
            'user_phone_number' => 'nullable|string|max:20',
            'user_email' => 'required|email|max:255',
            // PROPERTY INFO
            'property_id' => 'required|integer',
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string',
            // ROOM INFO
            'room_name' => 'required|string|max:255',
            'room_id' => 'required|integer',
            // BOOKING TYPE
            'booking_type' => 'required|in:daily,monthly',
            'check_in' => 'required|date',
            'check_out' => $checkOutRules,
            // PRICING
            'daily_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'booking_days' => 'nullable|integer|min:0',
            // Renewal monthly duration is capped at 12 months per booking (matches new-booking rule).
            // The cumulative cap (today + 15 months) is enforced separately below after we
            // compute the resulting check_out, since this depends on the chain anchor.
            'booking_months' => 'nullable|integer|min:0|max:12',
            'admin_fees' => 'nullable|numeric|min:0',
            'service_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            // TRANSACTION TYPE
            'transaction_type' => 'nullable|string',
            // VOUCHER (optional)
            'voucher_code' => 'nullable|string|min:8|max:20',
            // DEPOSIT & PARKING
            'deposit_fee' => 'nullable|numeric|min:0',
            'parking_type' => 'nullable|string',
            'parking_fee' => 'nullable|numeric|min:0',
            'parking_duration' => 'nullable|integer|min:0',
            // RENEWAL FLAG
            'is_renewal' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Block deprecated BRI Manual payment method (old mobile app versions)
        if ($request->transaction_type && stripos($request->transaction_type, 'bri') !== false && stripos($request->transaction_type, 'manual') !== false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Metode pembayaran Transfer BRI Manual sudah tidak tersedia. Silakan update aplikasi Anda ke versi terbaru untuk menggunakan metode pembayaran lainnya.',
                'error_code' => 'PAYMENT_METHOD_DEPRECATED'
            ], 400);
        }

        try {
            // Retrieve original booking by order_id
            $originalTransaction = Transaction::where('order_id', $orderId)
                ->where('status', '1')
                ->first();

            if (!$originalTransaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Original booking not found'
                ], 404);
            }

            // Validate that only paid/completed bookings can be renewed
            if (!in_array($originalTransaction->transaction_status, ['paid', 'completed'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only paid or completed bookings can be renewed',
                    'current_status' => $originalTransaction->transaction_status
                ], 400);
            }

            /* Re-anchor the renewal start date to the latest PAID/COMPLETED non-renewed
               booking in this room+user chain. This guarantees that a previously
               cancelled renewal cannot push the new check-in forward — even if the
               client mistakenly sent a date taken from a cancelled row. */
            $latestPaidCheckOut = DB::table('t_transactions')
                ->where('room_id', $originalTransaction->room_id)
                ->where('user_id', $originalTransaction->user_id)
                ->whereRaw('LOWER(transaction_status) IN (?, ?)', ['paid', 'completed'])
                ->where('renewal_status', 0)
                ->whereNull('cancel_at')
                ->orderBy('check_out', 'desc')
                ->value('check_out');

            if ($latestPaidCheckOut) {
                $expectedCheckIn = Carbon::parse($latestPaidCheckOut)->format('Y-m-d');
                if ($request->check_in !== $expectedCheckIn) {
                    Log::warning('Renewal check-in mismatch — overriding with latest paid checkout', [
                        'order_id'        => $orderId,
                        'received'        => $request->check_in,
                        'expected'        => $expectedCheckIn,
                        'reason'          => 'Latest non-cancelled paid booking check_out used as authoritative source',
                    ]);
                    $request->merge(['check_in' => $expectedCheckIn]);
                }
            }

            // Renewal availability window — must be 0-90 days before check-out, with a
            // type-specific hard cutoff on the check-out day itself: 12:00 WIB for daily
            // (new guest can arrive after noon checkout), 21:00 WIB for monthly. Server-side
            // mirror of the modal's pre-open guard so a tampered client request is still rejected.
            if ($latestPaidCheckOut) {
                $now = Carbon::now(config('app.timezone'));
                $today0 = $now->copy()->startOfDay();
                $coDate0 = Carbon::parse($latestPaidCheckOut)->startOfDay();
                $daysUntilCheckOut = $today0->diffInDays($coDate0, false);
                $sameDayCutoffHour = $request->booking_type === 'monthly' ? 21 : 12;

                if ($daysUntilCheckOut > 90) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => __('booking.js.renewal_window_too_early'),
                    ], 422);
                }
                if ($daysUntilCheckOut < 0 || ($daysUntilCheckOut === 0 && $now->hour >= $sameDayCutoffHour)) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => __('booking.js.renewal_window_closed'),
                    ], 422);
                }
            }

            // Get room details to verify it still exists
            $room = Room::find($request->room_id);
            if (!$room) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found'
                ], 400);
            }

            // NOTE: Intentionally skip rental_status check for renewals
            // Renewals are allowed even if rental_status=1 (room is currently rented)
            // because the same tenant is extending their stay

            $checkIn = Carbon::parse($request->check_in)->startOfDay();
            $checkOut = Carbon::parse($request->check_out)->endOfDay();

            // Check availability for new dates ONLY if NOT a renewal
            // Renewals skip conflict check because the tenant is extending their current stay
            $isRenewal = $request->is_renewal == 1;

            if (!$isRenewal) {
                $conflictingBookings = DB::table('t_transactions')
                    ->where('property_id', $request->property_id)
                    ->where('room_id', $request->room_id)
                    ->where('order_id', '!=', $orderId) // Exclude the original booking
                    ->where('status', '1')
                    ->whereNotIn('transaction_status', ['cancelled', 'expired'])
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn)
                    ->limit(1)
                    ->get();

                if (!$conflictingBookings->isEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Room is not available for the selected dates',
                        'conflicting_bookings' => $conflictingBookings
                    ], 409);
                }
            }
            // NOTE: When is_renewal=1, we skip the conflict check entirely
            // because the tenant is extending their stay in the same room

            DB::beginTransaction();

            // Preserve original check-in day across renewal chain.
            // For renewals: copy from previous booking. For room changes: reset.
            $originalCheckinDay = $originalTransaction->original_checkin_day
                ?? Carbon::parse($originalTransaction->check_in)->day;

            // Set check-in time to 14:00:00 and check-out time to 12:00:00
            $checkInWithTime = Carbon::createFromFormat('Y-m-d', $request->check_in, config('app.timezone'))->setTime(14, 0, 0);

            // Determine booking type - prioritize explicit booking_type parameter
            $isDaily = $request->booking_type === 'daily';

            // For monthly renewals: recalculate checkout using original check-in day
            // to avoid losing days across renewals (e.g., Jan 31→Feb 28→Mar 31 not Mar 28)
            $bookingMonths = null;
            if (!$isDaily) {
                $bookingMonths = $request->booking_months ?? 1;
                $targetMonth = $checkInWithTime->month + $bookingMonths;
                $targetYear = $checkInWithTime->year + intdiv($targetMonth - 1, 12);
                $targetMonth = (($targetMonth - 1) % 12) + 1;
                $maxDay = Carbon::create($targetYear, $targetMonth, 1)->daysInMonth;
                $clampedDay = min($originalCheckinDay, $maxDay);
                $checkOutWithTime = Carbon::create($targetYear, $targetMonth, $clampedDay, 12, 0, 0);

                // Cumulative cap: the new check_out cannot extend more than 15 months from today.
                $maxAllowedCheckOut = Carbon::today(config('app.timezone'))->addMonths(15);
                if ($checkOutWithTime->gt($maxAllowedCheckOut)) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => __('booking.js.renewal_max_15_months'),
                    ], 422);
                }
            } else {
                $checkOutWithTime = Carbon::createFromFormat('Y-m-d', $request->check_out, config('app.timezone'))->setTime(12, 0, 0);
            }

            // Calculate pricing based on booking type
            $bookingDays = null;
            $roomPrice = 0;

            if ($isDaily) {
                // DAILY BOOKING
                $bookingDays = $request->booking_days ?? $checkInWithTime->copy()->startOfDay()->diffInDays($checkOutWithTime->copy()->startOfDay());
                $dailyPrice = $request->daily_price ?? $room->price_original_daily ?? 0;
                $roomPrice = $dailyPrice * $bookingDays;
            } else {
                // MONTHLY BOOKING
                $bookingMonths = $request->booking_months ?? $checkInWithTime->copy()->startOfDay()->diffInMonths($checkOutWithTime->copy()->startOfDay());
                if ($bookingMonths < 1) {
                    $bookingMonths = 1;
                }
                $monthlyPrice = $request->monthly_price ?? $room->price_original_monthly ?? 0;
                $roomPrice = $monthlyPrice * $bookingMonths;
            }

            $adminFees = $request->admin_fees ?? 0;
            $serviceFees = $request->service_fees ?? 30000;
            $tax = $request->tax ?? 0;

            $subtotalBeforeServiceFee = $roomPrice + $adminFees + $tax;

            // Voucher processing
            $voucherId = null;
            $voucherCode = null;
            $discountAmount = 0;
            $subtotalBeforeDiscount = $subtotalBeforeServiceFee;

            if ($request->filled('voucher_code')) {
                $voucherService = app(VoucherService::class);

                // Validate and apply voucher
                $voucherValidation = $voucherService->validateVoucher(
                    $request->voucher_code,
                    $request->user_id,
                    $subtotalBeforeServiceFee,
                    $request->property_id,
                    $request->room_id
                );

                if (!$voucherValidation['valid']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Voucher validation failed',
                        'errors' => $voucherValidation['errors']
                    ], 422);
                }

                $voucher = $voucherValidation['voucher'];
                $calculation = $voucherService->calculateDiscount($voucher, $subtotalBeforeServiceFee);

                $voucherId = $voucher->idrec;
                $voucherCode = $voucher->code;
                $discountAmount = $calculation['discount_amount'];

                // Increment voucher usage count
                $voucher->increment('current_usage_count');
            }

            // Handle deposit fee
            $depositFee = $request->deposit_fee ?? 0;

            // Handle parking fee - store as is (no calculation with duration)
            $parkingDuration = $request->parking_duration ?? 0;
            $parkingFee = $request->parking_fee ?? 0;
            $parkingType = $request->parking_type ?? null;

            // Calculate final grand total: Subtotal - Discount + Service Fee + Deposit + Parking
            $grandtotalPrice = $subtotalBeforeServiceFee - $discountAmount + $serviceFees + $depositFee + $parkingFee;

            // Generate new order_id
            $property = Property::find($request->property_id);
            $propertyInitial = $property && $property->initial ? $property->initial : 'XX';
            do {
                $randomNumber = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
                $newOrderId = 'UMH-' . now()->format('ymd') . $randomNumber . $propertyInitial;
            } while (Transaction::where('order_id', $newOrderId)->exists());

            // Set expiration time to 30 minutes from now
            $expiredAt = now()->addMinutes(30);

            // Server-authoritative source for the renewal's room.
            // The client may send a stale room_id cached from a pre-transfer snapshot
            // of t_transactions (which historically lagged behind room transfers), so
            // we resolve the room from the parent order's currently-active t_booking
            // row instead and ignore the client-supplied values when available.
            // Falls back to the request payload only if there is no active booking.
            $activeParentBooking = Booking::where('order_id', $orderId)->where('status', '1')->first();
            $authRoomId = $activeParentBooking?->room_id ?? $request->room_id;
            $authRoomName = $request->room_name;
            if ($activeParentBooking && $activeParentBooking->room_id) {
                $authRoom = Room::find($activeParentBooking->room_id);
                if ($authRoom && $authRoom->name) {
                    $authRoomName = $authRoom->name;
                }
            }

            // Prepare transaction data from request
            $transactionData = [
                // USER DATA
                'user_id' => $request->user_id,
                'user_name' => $request->user_name,
                'user_phone_number' => $request->user_phone_number,
                'user_email' => $request->user_email,
                // PROPERTY DATA
                'property_id' => $request->property_id,
                'property_name' => $request->property_name,
                'property_type' => $request->property_type,
                // ROOM DATA — server-authoritative (see $authRoomId resolution above)
                'room_id' => $authRoomId,
                'room_name' => $authRoomName,
                // ORDER DETAILS
                'order_id' => $newOrderId,
                'transaction_date' => now(),
                'booking_type' => $request->booking_type,
                'booking_days' => $bookingDays,
                'booking_months' => $bookingMonths,
                // PRICES
                'room_price' => $roomPrice,
                'daily_price' => $isDaily ? ($request->daily_price ?? $room->price_original_daily) : null,
                'monthly_price' => !$isDaily ? ($request->monthly_price ?? $room->price_original_monthly) : null,
                'admin_fees' => $adminFees,
                'service_fees' => $serviceFees,
                'tax' => $tax,
                'grandtotal_price' => $grandtotalPrice,
                // VOUCHER DATA
                'voucher_id' => $voucherId,
                'voucher_code' => $voucherCode,
                'discount_amount' => $discountAmount,
                'subtotal_before_discount' => $subtotalBeforeDiscount,
                // DEPOSIT & PARKING
                'deposit_fee' => $depositFee,
                'parking_type' => $parkingType,
                'parking_fee' => $parkingFee,
                'parking_duration' => $parkingDuration,
                // CODE AND STATUS
                'transaction_type' => $request->transaction_type ?? 'BRI Manual',
                'transaction_code' => 'TRX-' . Str::random(16),
                'transaction_status' => 'pending',
                'status' => '1',
                // DATES
                'check_in' => $checkInWithTime,
                'check_out' => $checkOutWithTime,
                // Carry forward original check-in day for future renewals
                'original_checkin_day' => $originalCheckinDay,
                'expired_at' => $expiredAt,
                // RENEWAL FLAG
                'is_renewal' => $request->is_renewal ?? 1,
            ];

            // Create new transaction
            $newTransaction = Transaction::create($transactionData);

            // Log voucher usage if voucher was applied
            if ($voucherId && $voucherCode) {
                $voucherService = app(VoucherService::class);
                $voucherService->logUsage([
                    'voucher_id' => $voucherId,
                    'voucher_code' => $voucherCode,
                    'user_id' => $request->user_id,
                    'order_id' => $newOrderId,
                    'transaction_id' => $newTransaction->idrec,
                    'property_id' => $request->property_id,
                    'room_id' => $authRoomId,
                    'original_amount' => $subtotalBeforeDiscount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $grandtotalPrice
                ]);
            }

            // 1. Reuse the active parent booking already resolved above and close it.
            $oldBooking = $activeParentBooking;
            if ($oldBooking) {
                $oldBooking->update(['check_out_at' => now()]);
            }

            // 2. Clone old booking to new order_id with check_out_at = null
            $newBookingData = [
                'property_id' => $oldBooking ? $oldBooking->property_id : $request->property_id,
                'room_id' => $oldBooking ? $oldBooking->room_id : $request->room_id,
                'order_id' => $newOrderId,
                'user_name' => $oldBooking ? $oldBooking->user_name : $request->user_name,
                'user_email' => $oldBooking ? $oldBooking->user_email : $request->user_email,
                'user_phone_number' => $oldBooking ? $oldBooking->user_phone_number : $request->user_phone_number,
                'check_in_at' => $oldBooking ? $oldBooking->check_in_at : null,
                'ktp_img' => $oldBooking ? $oldBooking->ktp_img : null,
                'check_out_at' => null, // User hasn't checked out of new booking yet
                'doc_type' => $oldBooking ? $oldBooking->doc_type : null,
                'doc_path' => $oldBooking ? $oldBooking->doc_path : null,
                'created_by' => $oldBooking ? $oldBooking->created_by : $request->user_id,
                'updated_by' => $request->user_id,
                'status' => '1',
                'previous_booking_id' => null,
                'reason' => null,
                'description' => null,
                'room_changed_at' => null,
                'room_changed_by' => null,
                'is_printed' => null
            ];
            Booking::create($newBookingData);

            // Create new payment record
            $paymentData = [
                'property_id' => $request->property_id,
                'room_id' => $authRoomId,
                'order_id' => $newOrderId,
                'user_id' => $request->user_id,
                'grandtotal_price' => $grandtotalPrice,
                'payment_status' => 'unpaid',
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            Payment::create($paymentData);

            // Update original transaction's renewal_status to 1 (already renewed)
            $originalTransaction->update(['renewal_status' => 1]);

            /* rental_status untouched — physical occupancy doesn't change on
               renewal; the guest is still in the room (or not) regardless. */

            // Note: Do NOT increment parking quota for renewals - user is extending existing parking slot

            // Log renewal
            Log::info("Booking renewed successfully", [
                'original_order_id' => $orderId,
                'new_order_id' => $newOrderId,
                'user_id' => $request->user_id,
                'is_renewal' => $request->is_renewal ?? 1,
                'renewal_status_updated' => true,
                'expired_at' => $expiredAt
            ]);

            // Send booking confirmation email
            if ($request->user_email) {
                $user = new \App\Models\User();
                $user->email = $request->user_email;
                $user->id = $request->user_id;

                $this->sendEmailBooking(
                    $user,
                    $newBookingData,
                    $transactionData,
                    null // Payment URL will be generated separately if needed
                );
            }

            DB::commit();

            // Send push notifications for booking renewal
            try {
                $firebaseService = new FirebaseNotificationService();
                $propertyName = $property ? $property->property_name : 'property';
                $userName = $newBookingData['user_name'] ?? 'Guest';

                // Notify guest
                $guestUser = User::find($request->user_id);
                if ($guestUser) {
                    $firebaseService->sendToUser(
                        $guestUser,
                        'Booking Renewed',
                        "Your stay at {$propertyName} has been extended. New order: {$newOrderId}.",
                        ['type' => 'booking_renewed', 'order_id' => $newOrderId]
                    );
                }

                // Notify admins
                $firebaseService->sendToAdmins(
                    'Booking Renewed',
                    "{$userName} renewed booking at {$propertyName}. Order: {$newOrderId}.",
                    ['type' => 'booking_renewed', 'order_id' => $newOrderId]
                );
            } catch (\Exception $e) {
                Log::warning('Push notification failed for booking renewal', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Booking renewed successfully',
                'data' => [
                    'original_order_id' => $orderId,
                    'new_order_id' => $newOrderId,
                    'is_renewal' => $request->is_renewal ?? 1,
                    'transaction' => $newTransaction->toArray(),
                    'expired_at' => $expiredAt->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking renewal failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'original_order_id' => $orderId
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error renewing booking',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);
            
            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'transaction_status' => 'sometimes|required',
                'status' => 'sometimes|required',
                'paid_at' => 'sometimes|required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transaction->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Booking updated successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified booking by order_id
     *
     * @param  string  $order_id
     * @return \Illuminate\Http\Response
     */
    public function showByOrderId($order_id)
    {
        try {
            $booking = Transaction::where('order_id', $order_id)->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }

            $data = $booking->toArray();
            $data['check_in_at'] = $booking->getCheckInAt();

            // Get room no from m_rooms table
            if ($booking->room_id) {
                $room = DB::table('m_rooms')->where('idrec', $booking->room_id)->first();
                $data['room_no'] = $room ? $room->no : null;
            } else {
                $data['room_no'] = null;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Booking retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }    
    /**
         * Display bookings for a specific user
         *
         * @param  int  $user_id
         * @return \Illuminate\Http\Response
         */
        public function showByUserId($user_id)
        {
            try {
                $bookings = Transaction::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($bookings->isEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No bookings found for this user'
                    ], 404);
                }

                $data = $bookings->map(function ($booking) {
                    $bookingData = $booking->toArray();
                    $bookingData['check_in_at'] = $booking->getCheckInAt();

                    // Get room no from m_rooms table
                    if ($booking->room_id) {
                        $room = DB::table('m_rooms')->where('idrec', $booking->room_id)->first();
                        $bookingData['room_no'] = $room?->no;
                    } else {
                        $bookingData['room_no'] = null;
                    }

                    return $bookingData;
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'Bookings retrieved successfully',
                    'data' => $data
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving bookings',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

    /**
     * Upload attachment for a booking
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachment(Request $request, $id)
    {
        try {
            // Validate the ID first
            $booking = DB::table('t_transactions')
                ->where('idrec', $id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }


            $validator = Validator::make($request->all(), [
                'attachment_file' => 'required|string', // Changed from file to string
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }


            $base64Image = $request->input('attachment_file');
            
            // Remove data URL prefix if present
            if (strpos($base64Image, ';base64,') !== false) {
                [$_, $base64Image] = explode(';', $base64Image);
                [$_, $base64Image] = explode(',', $base64Image);
            }


            // Validate base64 image
            $imageData = base64_decode($base64Image, true);
            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid base64 image'
                ], 422);
            }


            // Validate image type
            $f = finfo_open();
            $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
            if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid image type. Only JPEG and PNG are allowed.'
                ], 422);
            }


            // Update the booking with the base64 image
            $updateData = ['attachment' => $base64Image];

            // Only update status to 'waiting' if it's not already 'waiting'
            if ($booking->transaction_status !== 'waiting') {
                $updateData['transaction_status'] = 'waiting';
            }

            $updated = DB::table('t_transactions')
                ->where('idrec', $id)
                ->update($updateData);

            if (!$updated) {
                throw new \Exception('Failed to update booking with attachment');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attachment uploaded successfully',
                'data' => [
                    'booking_id' => $id,
                    'attachment_uploaded' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading attachment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attachment for a booking
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAttachment(Request $request, $id)
    {
        try {
            // Validate the ID first
            $booking = DB::table('t_transactions')
                ->where('idrec', $id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'attachment_file' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $base64Image = $request->input('attachment_file');
            
            // Remove data URL prefix if present
            if (strpos($base64Image, ';base64,') !== false) {
                [$_, $base64Image] = explode(';', $base64Image);
                [$_, $base64Image] = explode(',', $base64Image);
            }

            // Validate base64 image
            $imageData = base64_decode($base64Image, true);
            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid base64 image'
                ], 422);
            }

            // Validate image type
            $f = finfo_open();
            $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
            if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid image type. Only JPEG and PNG are allowed.'
                ], 422);
            }

            // Update the booking with the new base64 image
            $updateData = [
                'attachment' => $base64Image,
                'updated_at' => now()
            ];

            // Only update status to 'waiting' if it's not already 'waiting'
            if ($booking->transaction_status !== 'waiting') {
                $updateData['transaction_status'] = 'waiting';
            }

            $updated = DB::table('t_transactions')
                ->where('idrec', $id)
                ->update($updateData);

            if (!$updated) {
                throw new \Exception('Failed to update booking attachment');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attachment updated successfully',
                'data' => [
                    'booking_id' => $id,
                    'attachment_updated' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating attachment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment method for a booking
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required|string',
                'virtual_account_no' => 'nullable|string',
                'payment_bank' => 'nullable|string',
                'deposit_fee' => 'nullable|numeric|min:0',
                'parking_duration' => 'nullable|integer|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'parking_type' => 'nullable|string',
                'voucher_code' => 'nullable|string',
                'discount_amount' => 'nullable|numeric|min:0',
                'vehicle_plate' => 'nullable|string|max:20',
                'owner_name' => 'nullable|string|max:100',
                'owner_phone' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find the booking
            $booking = DB::table('t_transactions')
                ->where('idrec', $id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found'
                ], 404);
            }

            // Prepare update data
            $updateData = [
                'transaction_type' => $request->payment_method,
                'paid_at' => null,
                'updated_at' => now()
            ];

            // Add virtual account number if provided
            if ($request->has('virtual_account_no')) {
                $updateData['virtual_account_no'] = $request->virtual_account_no;
            }

            // Add bank if provided
            if ($request->has('payment_bank')) {
                $updateData['payment_bank'] = $request->payment_bank;
            }

            // Handle deposit fee — fall back to DB value if not provided
            $depositFee = floatval($request->input('deposit_fee', $booking->deposit_fee ?? 0));
            $updateData['deposit_fee'] = $depositFee;

            // Handle parking fee - store as is (no calculation with duration)
            $parkingDuration = 0;
            $parkingFee = 0;

            if ($request->has('parking_fee')) {
                $parkingFee = floatval($request->parking_fee);
            }
            if ($request->has('parking_duration')) {
                $parkingDuration = intval($request->parking_duration);
            }

            $updateData['parking_fee'] = $parkingFee;
            $updateData['parking_duration'] = $parkingDuration;

            // Handle parking type
            if ($request->has('parking_type')) {
                $updateData['parking_type'] = $request->parking_type;
            }

            // Handle voucher discount
            $discountAmount = floatval($booking->discount_amount ?? 0);
            if ($request->has('discount_amount')) {
                $discountAmount = floatval($request->discount_amount);
                $updateData['discount_amount'] = $discountAmount;
            }
            if ($request->has('voucher_code')) {
                $updateData['voucher_code'] = $request->voucher_code;
            }

            // Recalculate grandtotal if fees changed
            // Formula: Grandtotal = (room_price + admin_fees) - discount + service_fees + deposit + parking
            $roomPrice = floatval($booking->room_price ?? 0);
            $adminFees = floatval($booking->admin_fees ?? 0);
            $serviceFees = floatval($booking->service_fees ?? 0);

            $subtotal = $roomPrice + $adminFees;
            $newGrandtotal = $subtotal - $discountAmount + $serviceFees + $depositFee + $parkingFee;

            $updateData['grandtotal_price'] = $newGrandtotal;
            $updateData['subtotal_before_discount'] = $subtotal;

            // Update payment method and fees
            $updated = DB::table('t_transactions')
                ->where('idrec', $id)
                ->update($updateData);

            // Also update t_payment grandtotal_price
            DB::table('t_payment')
                ->where('order_id', $booking->order_id)
                ->update(['grandtotal_price' => $newGrandtotal]);

            // Insert a new t_parking row for this booking/renewal — multi-row design
            // (see 2026-05-08 rebuild): every paid period gets its own record, never an
            // in-place update of a previous renewal's row.
            if ($parkingFee > 0 && $request->parking_type && $request->parking_type !== 'none') {
                try {
                    $user = Auth::user();

                    // Compute parking period from the booking dates:
                    //   start_rent = check_in
                    //   end_rent   = MIN(check_in + duration months, check_out)
                    $startRent = null;
                    $endRent = null;
                    if (!empty($booking->check_in)) {
                        $startCarbon = \Carbon\Carbon::parse($booking->check_in)->startOfDay();
                        $endCarbon = $startCarbon->copy()->addMonths(max(1, (int) $parkingDuration));
                        if (!empty($booking->check_out)) {
                            $checkOutCarbon = \Carbon\Carbon::parse($booking->check_out)->startOfDay();
                            if ($endCarbon->gt($checkOutCarbon)) {
                                $endCarbon = $checkOutCarbon;
                            }
                        }
                        $startRent = $startCarbon->toDateString();
                        $endRent = $endCarbon->toDateString();
                    }

                    // For renewals, look up the most-recent prior parking row to detect
                    // a type change (quota swap) and to carry forward plate/owner data
                    // when the request omits them. Initial bookings always increment quota.
                    $isRenewal = (int) ($booking->is_renewal ?? 0) === 1;
                    $existingParking = $isRenewal
                        ? DB::table('t_parking')
                            ->where('user_id', Auth::id())
                            ->where('property_id', $booking->property_id)
                            ->whereNull('deleted_at')
                            ->orderByDesc('idrec')
                            ->first()
                        : null;

                    if ($existingParking && $existingParking->parking_type !== $request->parking_type) {
                        $this->decrementParkingQuota($booking->property_id, $existingParking->parking_type);
                        $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                    } elseif (!$existingParking) {
                        // Initial booking, OR first-time parking purchase on a renewal
                        $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                    }

                    DB::table('t_parking')->insert([
                        'property_id' => $booking->property_id,
                        'order_id' => $booking->order_id,
                        'parking_type' => $request->parking_type,
                        'vehicle_plate' => $request->vehicle_plate ?? ($existingParking->vehicle_plate ?? null),
                        'owner_name' => $request->owner_name ?? ($existingParking->owner_name ?? ($user->name ?? null)),
                        'owner_phone' => $request->owner_phone ?? ($existingParking->owner_phone ?? ($user->phone_number ?? null)),
                        'user_id' => Auth::id(),
                        'parking_duration' => (int) $parkingDuration ?: 1,
                        'start_rent' => $startRent,
                        'end_rent' => $endRent,
                        'fee_amount' => $parkingFee,
                        'status' => 1,
                        'management_only' => 0,
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to insert parking record: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment method updated successfully',
                'data' => [
                    'booking_id' => $id,
                    'payment_method' => $request->payment_method,
                    'payment_bank' => $request->payment_bank,
                    'virtual_account_no' => $request->virtual_account_no,
                    'deposit_fee' => $depositFee,
                    'parking_duration' => $parkingDuration,
                    'parking_fee' => $parkingFee,
                    'parking_type' => $request->parking_type,
                    'vehicle_plate' => $request->vehicle_plate,
                    'discount_amount' => $discountAmount,
                    'grandtotal_price' => $newGrandtotal,
                    'transaction_status' => 'pending'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating payment method',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send booking confirmation email to user
     *
     * @param \App\Models\User $user
     * @param array $bookingData
     * @param array $transactionData
     * @param string|null $paymentUrl
     * @return void
     */
    public function sendEmailBooking($user, $bookingData, $transactionData, $paymentUrl = null)
    {
        try {
            $notification = new BookingConfirmationNotification(
                $bookingData,
                $transactionData,
                $paymentUrl
            );

            // Use SMTP to send email directly
            $result = $notification->sendViaSMTP($user);

            if ($result) {
                Log::info('Booking confirmation email sent via SMTP', [
                    'order_id' => $transactionData['order_id'] ?? null,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            } else {
                Log::warning('Booking confirmation email via SMTP returned false', [
                    'order_id' => $transactionData['order_id'] ?? null,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            Log::error('Failed to send booking confirmation email via SMTP', [
                'order_id' => $transactionData['order_id'] ?? null,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Generate a secure signature for Doku API authentication
     * 
     * This method creates an HMAC-SHA256 signature using the provided credentials and request data.
     * The signature is used to authenticate requests to the Doku payment gateway.
     *
     * @param string $clientId     The Doku API client ID
     * @param string $requestId    Unique request identifier (UUID)
     * @param string $timestamp    Request timestamp in ISO 8601 format
     * @param string $requestTarget API endpoint path (e.g., '/checkout/v1/payment')
     * @param string $requestBody  JSON-encoded request payload
     * @param string $secretKey    Doku API secret key for signature generation
     * 
     * @return string HMAC-SHA256 signature prefixed with 'HMACSHA256='
     * 
     * @throws \InvalidArgumentException If any required parameter is empty or invalid
     */
    private function generateDokuSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $requestTarget,
        string $requestBody,
        string $secretKey
    ): string {
        // Input validation
        if (empty($clientId) || empty($requestId) || empty($timestamp) || 
            empty($requestTarget) || empty($secretKey)) {
            throw new \InvalidArgumentException('All parameters are required for signature generation');
        }

        // Generate digest from request body
        $digest = base64_encode(hash('sha256', $requestBody, true));
        
        // Prepare signature components in exact order required by Doku
        $signatureComponents = "Client-Id:{$clientId}\n" .
                             "Request-Id:{$requestId}\n" .
                             "Request-Timestamp:{$timestamp}\n" .
                             "Request-Target:{$requestTarget}\n" .
                             "Digest:{$digest}";
        
        // Generate HMAC-SHA256 signature
        $signature = base64_encode(hash_hmac('sha256', $signatureComponents, $secretKey, true));
        
        return 'HMACSHA256=' . $signature;
    }

    /**
     * Process payment through Doku payment gateway (API Endpoint)
     *
     * This endpoint handles the entire payment flow with Doku, including:
     * - Request validation
     * - Request preparation
     * - Signature generation
     * - API communication
     * - Response handling
     * - Error management
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processDokuPaymentEndpoint(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string',
                'transaction_code' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'property_name' => 'nullable|string',
                'room_name' => 'nullable|string',
                'user_name' => 'nullable|string',
                'user_email' => 'required|email',
                'user_phone' => 'required|string',
                'user_address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->processDokuPayment($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Payment request processed successfully',
                'data' => $result
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input: ' . $e->getMessage()
            ], 400);

        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Unexpected error in processDokuPayment endpoint', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Process payment through Doku payment gateway (Internal)
     *
     * This method handles the entire payment flow with Doku, including:
     * - Request preparation
     * - Signature generation
     * - API communication
     * - Response handling
     * - Error management
     *
     * @param array $data {
     *     Required payment data
     *
     *     @type string $order_id          Unique order identifier
     *     @type string $transaction_code  Internal transaction reference
     *     @type float  $amount            Payment amount
     *     @type string $property_name     Name of the property being booked
     *     @type string $room_name         Name of the room being booked
     *     @type string $user_name         Full name of the customer
     *     @type string $user_email        Customer's email address
     *     @type string $user_phone        Customer's phone number
     * }
     *
     * @return array{
     *     payment_url: string|null,
     *     expired_at: string|null,
     *     response: array
     * }
     *
     * @throws \RuntimeException If payment processing fails
     * @throws \InvalidArgumentException If required parameters are missing
     */
    private function processDokuPayment(array $data): array
    {
        try {
            // Input validation
            $requiredFields = ['order_id', 'transaction_code', 'amount', 'user_email', 'user_phone'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \InvalidArgumentException("Missing required field: {$field}");
                }
            }

            // Get API configuration
            $dokuUrl = config('services.doku.api_url', 'https://api-sandbox.doku.com/checkout/v1/payment');
            $clientId = config('services.doku.client_id');
            $secretKey = config('services.doku.secret_key');
            
            if (empty($clientId) || empty($secretKey)) {
                throw new \RuntimeException('Payment gateway configuration is incomplete');
            }

            // Prepare request data with type safety
            $requestData = [
                'order' => [
                    'amount' => (float) $data['amount'],
                    'invoice_number' => (string) $data['order_id'],
                    'currency' => 'IDR',
                    'session_id' => (string) $data['transaction_code'],
                    'callback_url' => config('app.url') . '/api/payment/callback',
                    'line_items' => [
                        [
                            'name' => sprintf('%s - %s', $data['property_name'] ?? 'Property', $data['room_name'] ?? 'Room'),
                            'price' => (float) $data['amount'],
                            'quantity' => 1
                        ]
                    ]
                ],
                'payment' => [
                    'payment_due_date' => 60 // 60 minutes
                ],
                'customer' => [
                    'name' => $data['user_name'] ?? 'Customer',
                    'email' => $data['user_email'],
                    'phone' => $data['user_phone'],
                    'address' => $data['user_address'] ?? 'Indonesia',
                    'country' => 'ID'
                ]
            ];

            // Generate request metadata
            $requestId = (string) Str::uuid();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
            $requestTarget = '/checkout/v1/payment';
            $requestBody = json_encode($requestData);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Failed to encode request data: ' . json_last_error_msg());
            }

            // Generate and validate signature
            $signature = $this->generateDokuSignature(
                $clientId,
                $requestId,
                $timestamp,
                $requestTarget,
                $requestBody,
                $secretKey
            );

            // Execute API request with timeout and retry
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->withHeaders([
                    'Client-Id' => $clientId,
                    'Request-Id' => $requestId,
                    'Request-Timestamp' => $timestamp,
                    'Signature' => $signature,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($dokuUrl, $requestData);

            $responseData = $response->json() ?? [];

            // Handle API errors
            if (!$response->successful()) {
                $errorDetails = [
                    'status' => $response->status(),
                    'error' => $responseData['error'] ?? null,
                    'code' => $responseData['error_code'] ?? null,
                    'message' => $responseData['message'] ?? 'Unknown error',
                    'request_id' => $requestId
                ];
                
                \Log::error('Doku API Request Failed', $errorDetails);
                throw new \RuntimeException($errorDetails['message'], $errorDetails['code'] ?? 0);
            }

            return [
                'payment_url' => $responseData['response']['payment']['url'] ?? null,
                'expired_at' => $responseData['response']['payment']['expired_datetime'] ?? null,
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            \Log::error('Payment Processing Error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \RuntimeException(
                'Payment processing failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get DOKU B2B Access Token (with caching)
     * Makes a POST request to DOKU sandbox API to obtain B2B access token
     * Token is cached for 14 minutes (840 seconds) as it expires in 15 minutes
     *
     * @param bool $forceRefresh Force refresh token even if cached
     * @return array
     * @throws \Exception
     */
    private function dokuGetTokenB2B(bool $forceRefresh = false): array
    {
        $cacheKey = 'doku_b2b_token';

        // Try to get cached token if not forcing refresh
        if (!$forceRefresh) {
            $cachedToken = \Cache::get($cacheKey);
            if ($cachedToken) {
                return $cachedToken;
            }
        }

        try {
            // Get DOKU configuration
            $sandboxUrl = config('services.doku.api_url');
            $clientId = config('services.doku.client_id');
            $privateKey = config('services.doku.private_key'); // This should contain the RSA private key in PEM format

            if (empty($sandboxUrl) || empty($clientId) || empty($privateKey)) {
                throw new \RuntimeException('DOKU configuration is incomplete');
            }

            // Prepare request data
            $requestBody = [
                'grantType' => 'client_credentials'
            ];

            // Generate timestamp in ISO 8601 format with timezone (Asia/Jakarta)
            // Format: 2026-01-05T00:56:05+07:00
            $timestamp = Carbon::now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');

            // Generate signature for B2B token request
            // Format: ClientId|Timestamp
            $stringToSign = $clientId . '|' . $timestamp;

            // Sign using RSA-SHA256 with private key
            $privateKeyResource = openssl_pkey_get_private($privateKey);

            if ($privateKeyResource === false) {
                throw new \RuntimeException('Invalid private key format');
            }

            $binarySignature = '';
            $signSuccess = openssl_sign($stringToSign, $binarySignature, $privateKeyResource, OPENSSL_ALGO_SHA256);

            if (!$signSuccess) {
                throw new \RuntimeException('Failed to generate signature');
            }

            $signature = base64_encode($binarySignature);

            // API endpoint
            $endpoint = $sandboxUrl . '/authorization/v1/access-token/b2b';

            // Make POST request to DOKU
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-client-key' => $clientId,
                    'x-timestamp' => $timestamp,
                    'x-signature' => $signature,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($endpoint, $requestBody);

            $responseData = $response->json() ?? [];

            // Check if request was successful
            if (!$response->successful()) {
                $errorDetails = [
                    'status' => $response->status(),
                    'response' => $responseData,
                    'error_code' => $responseData['responseCode'] ?? null,
                    'error_message' => $responseData['responseMessage'] ?? 'Unknown error'
                ];

                \Log::error('DOKU B2B Token Request Failed', $errorDetails);

                throw new \RuntimeException(
                    $errorDetails['error_message'],
                    $errorDetails['status']
                );
            }

            $result = [
                'success' => true,
                'access_token' => $responseData['accessToken'] ?? null,
                'token_type' => $responseData['tokenType'] ?? 'Bearer',
                'expires_in' => $responseData['expiresIn'] ?? null,
                'response_code' => $responseData['responseCode'] ?? null,
                'response_message' => $responseData['responseMessage'] ?? null,
                'additional_info' => $responseData['additionalInfo'] ?? null
            ];

            // Cache the token for 14 minutes (840 seconds) - 1 minute before expiration
            $expiresIn = $responseData['expiresIn'] ?? 900;
            $cacheSeconds = max(60, $expiresIn - 60); // Cache for expires_in - 60 seconds, minimum 60 seconds
            \Cache::put($cacheKey, $result, $cacheSeconds);

            return $result;

        } catch (\Exception $e) {
            \Log::error('DOKU B2B Token Request Exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }
    /**
     * Test endpoint for DOKU B2B Token Generation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testDokuGetTokenB2B()
    {
        try {
            $result = $this->dokuGetTokenB2B();

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'DOKU B2B token generated successfully',
                    'data' => $result
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate DOKU B2B token',
                    'error' => $result['error'] ?? 'Unknown error',
                    'code' => $result['code'] ?? 0
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred while testing DOKU B2B token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalize a phone number to DOKU's preferred format: 62XXXXXXXXXX.
     * Strips every non-digit character (spaces, dashes, +, parens, dots, etc.),
     * then rewrites the country prefix:
     *   0XXX   -> 62XXX   (Indonesian local format)
     *   62XXX  -> 62XXX   (already correct)
     *   620XXX -> 62XXX   (malformed double prefix)
     *   8XXX   -> 628XXX  (bare digits, assume Indonesia)
     * Returns empty string for empty/null input so DOKU can treat phone as optional.
     */
    private function normalizeDokuPhone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '620')) {
            $digits = '62' . ltrim(substr($digits, 2), '0');
        } elseif (str_starts_with($digits, '62')) {
            // already prefixed
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . ltrim($digits, '0');
        } else {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Generate Virtual Account via DOKU
     *
     * @param array $data Payment and customer data
     * @return array
     * @throws \Exception
     */
    private function dokuGenerateVA(array $data): array
    {
        try {
            // Step 1: Get B2B access token
            $tokenResult = $this->dokuGetTokenB2B();

            if (!$tokenResult['success']) {
                throw new \RuntimeException('Failed to get B2B token: ' . ($tokenResult['error'] ?? 'Unknown error'));
            }

            $accessToken = $tokenResult['access_token'];

            // Get DOKU configuration
            $sandboxUrl = config('services.doku.api_url');
            $clientId = config('services.doku.client_id');
            $privateKey = config('services.doku.private_key');

            if (empty($sandboxUrl) || empty($clientId) || empty($privateKey)) {
                throw new \RuntimeException('DOKU configuration is incomplete');
            }

            // Determine bank and get corresponding DGPC code
            $bank = strtoupper($data['bank'] ?? 'BRI'); // Default to BRI if not specified
            $banks = config('services.doku.banks');

            if (!isset($banks[$bank])) {
                throw new \InvalidArgumentException("Invalid bank selection: {$bank}. Available banks: " . implode(', ', array_keys($banks)));
            }

            $bankConfig = $banks[$bank];
            $dgpcCode = $bankConfig['dgpc'];
            $channel = $data['channel'] ?? $bankConfig['channel'];

            // Generate timestamp in ISO 8601 format with timezone (Asia/Jakarta)
            $timestamp = Carbon::now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');

            // Generate external ID (order_id)
            $externalId = $data['order_id'];

            // Prepare partnerServiceId and customerNo according to DOKU rules
            // partnerServiceId: The DGPC code padded to 8 characters with leading spaces
            // For most banks, use the full DGPC code and pad with spaces if needed
            $partnerServiceId = str_pad($dgpcCode, 8, ' ', STR_PAD_LEFT);

            // Generate customer number (4-5 digits random number)
            $customerNo = $data['customer_no'] ?? (string)mt_rand(10000, 99999);

            // Virtual Account Number = partnerServiceId (8 chars) + customerNo (5 chars) = 13 chars total
            // Note: partnerServiceId with spaces will be sent in the request
            $virtualAccountNo = $partnerServiceId . $customerNo;

            // Temporary debug logging
            if (config('app.debug')) {
                \Log::debug('DOKU VA Number Generation', [
                    'bank' => $bank,
                    'dgpc_code' => $dgpcCode,
                    'dgpc_length' => strlen($dgpcCode),
                    'partner_service_id' => $partnerServiceId,
                    'partner_service_id_length' => strlen($partnerServiceId),
                    'customer_no' => $customerNo,
                    'customer_no_length' => strlen($customerNo),
                    'virtual_account_no' => $virtualAccountNo,
                    'virtual_account_no_length' => strlen($virtualAccountNo),
                    'partner_service_id_hex' => bin2hex($partnerServiceId)
                ]);
            }

            // Calculate expiration date (30 minutes from now — matches t_transactions.expired_at)
            $expiredDate = Carbon::now('Asia/Jakarta')->addMinutes(30)->format('Y-m-d\TH:i:sP');

            $requestBody = [
                'partnerServiceId' => $partnerServiceId,
                'customerNo' => $customerNo,
                'virtualAccountNo' => $virtualAccountNo,
                'virtualAccountName' => $data['user_name'],
                'virtualAccountEmail' => $data['user_email'],
                'virtualAccountPhone' => $this->normalizeDokuPhone($data['user_phone'] ?? null),
                'trxId' => $externalId,
                'totalAmount' => [
                    'value' => number_format($data['amount'], 2, '.', ''),
                    'currency' => 'IDR'
                ],
                'additionalInfo' => [
                    'channel' => $channel,
                    'virtualAccountConfig' => [
                        'reusableStatus' => $data['reusable'] ?? true
                    ]
                ],
                'virtualAccountTrxType' => 'C',
                'expiredDate' => $expiredDate,
                'freeText' => [
                    [
                        'english' => 'PEMBELIAN',
                        'indonesia' => 'PEMBELIAN'
                    ]
                ]
            ];

            // Generate signature for VA creation
            // For B2B2C APIs, signature uses HMAC-SHA512 with secret_key
            // Format: HTTPMethod:RelativeUrl:AccessToken:RequestBodyHash:Timestamp

            // Minify JSON (remove spaces, no pretty print)
            $requestBodyJson = json_encode($requestBody, JSON_UNESCAPED_SLASHES);
            $httpMethod = 'POST';
            $relativeUrl = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va';

            // Create lowercase SHA256 hash of minified request body
            $requestBodyHash = hash('sha256', $requestBodyJson);

            // String to sign format: HTTPMethod:RelativeUrl:AccessToken:RequestBodyHash:Timestamp
            $stringToSign = $httpMethod . ':' . $relativeUrl . ':' . $accessToken . ':' . $requestBodyHash . ':' . $timestamp;

            // Get secret key for HMAC signature
            $secretKey = config('services.doku.secret_key');

            // Generate HMAC-SHA512 signature with secret key
            $signature = base64_encode(hash_hmac('sha512', $stringToSign, $secretKey, true));

            // API endpoint
            $endpoint = $sandboxUrl . '/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va';

            // Make POST request to DOKU
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-PARTNER-ID' => $clientId,
                    'X-EXTERNAL-ID' => $externalId,
                    'X-TIMESTAMP' => $timestamp,
                    'X-SIGNATURE' => $signature,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CHANNEL-ID' => 'H2H',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($endpoint, $requestBody);

            $responseData = $response->json() ?? [];

            // Check if request was successful
            if (!$response->successful()) {
                $errorDetails = [
                    'status' => $response->status(),
                    'response' => $responseData,
                    'error_code' => $responseData['responseCode'] ?? null,
                    'error_message' => $responseData['responseMessage'] ?? 'Unknown error'
                ];

                \Log::error('DOKU VA Creation Failed', $errorDetails);

                throw new \RuntimeException(
                    $errorDetails['error_message'],
                    $errorDetails['status']
                );
            }

            // Extract how to pay page
            $howToPayPage = $responseData['virtualAccountData']['additionalInfo']['howToPayPage'] ?? null;
            $howToPayApi = $responseData['virtualAccountData']['additionalInfo']['howToPayApi'] ?? null;

            return [
                'success' => true,
                'bank' => $bank,
                'channel' => $channel,
                'partner_service_id' => $partnerServiceId,
                'virtual_account_no' => $responseData['virtualAccountData']['virtualAccountNo'] ?? null,
                'virtual_account_name' => $responseData['virtualAccountData']['virtualAccountName'] ?? null,
                'total_amount' => $responseData['virtualAccountData']['totalAmount']['value'] ?? null,
                'expired_date' => $responseData['virtualAccountData']['expiredDate'] ?? null,
                'how_to_pay_page' => $howToPayPage,
                'how_to_pay_api' => $howToPayApi,
                'response_code' => $responseData['responseCode'] ?? null,
                'response_message' => $responseData['responseMessage'] ?? null,
                'full_response' => $responseData
            ];

        } catch (\Exception $e) {
            \Log::error('DOKU VA Generation Exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Get available banks for VA generation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDokuAvailableBanks()
    {
        try {
            $banks = config('services.doku.banks');
            $availableBanks = [];

            foreach ($banks as $code => $config) {
                $availableBanks[] = [
                    'code' => $code,
                    'name' => $code,
                    'dgpc' => $config['dgpc'],
                    'channel' => $config['channel']
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Available banks retrieved successfully',
                'data' => $availableBanks
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving available banks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint for DOKU VA Generation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testDokuGenerateVA(Request $request)
    {
        try {
            // Get available banks for validation
            $banks = config('services.doku.banks');
            $availableBanks = implode(',', array_keys($banks));

            // Validate request
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string',
                'user_name' => 'required|string',
                'user_email' => 'required|email',
                'user_phone' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'bank' => 'nullable|string|in:' . $availableBanks,
                'customer_no' => 'nullable|string',
                'channel' => 'nullable|string'
            ], [
                'bank.in' => 'Invalid bank selection. Available banks: ' . $availableBanks
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Idempotency check: return existing VA if already generated
            $existingTransaction = Transaction::where('order_id', $request->order_id)->first();
            if ($existingTransaction && $existingTransaction->virtual_account_no) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Virtual Account already exists',
                    'data' => [
                        'success' => true,
                        'virtual_account_no' => $existingTransaction->virtual_account_no,
                        'bank' => $existingTransaction->payment_bank,
                        'total_amount' => $existingTransaction->total_amount,
                        'amount' => $existingTransaction->total_amount,
                    ]
                ], 200);
            }

            $result = $this->dokuGenerateVA($request->all());

            if ($result['success']) {
                // Update transaction with VA details
                $transaction = Transaction::where('order_id', $request->order_id)->first();

                if ($transaction) {
                    $transaction->update([
                        'virtual_account_no' => $result['virtual_account_no'],
                        'payment_bank' => $result['bank'], // Bank name in uppercase (e.g., MANDIRI, CIMB)
                        'transaction_type' => strtolower($result['bank']), // Bank name in lowercase (e.g., mandiri, cimb)
                    ]);

                    Log::info('Transaction updated with VA details', [
                        'order_id' => $request->order_id,
                        'virtual_account_no' => $result['virtual_account_no'],
                        'payment_bank' => $result['bank'],
                        'transaction_type' => strtolower($result['bank'])
                    ]);
                } else {
                    Log::warning('Transaction not found for VA update', [
                        'order_id' => $request->order_id
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'DOKU Virtual Account generated successfully',
                    'data' => $result
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate DOKU Virtual Account',
                    'error' => $result['error'] ?? 'Unknown error',
                    'code' => $result['code'] ?? 0
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('DOKU VA Test Exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred while testing DOKU VA generation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint for DOKU QRIS Generation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testDokuGenerateQris(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string',
                'amount' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->dokuGenerateQris($request->all());

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'DOKU QRIS generated successfully',
                    'data' => $result
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate DOKU QRIS',
                    'error' => $result['error'] ?? 'Unknown error'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('DOKU QRIS Test Exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred while testing DOKU QRIS generation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QRIS payment using DOKU SNAP API
     *
     * @param array $data Contains: order_id, amount
     * @return array Response with success status and QRIS data
     */
    private function dokuGenerateQris(array $data)
    {
        try {
            // Step 1: Get B2B access token
            $tokenResult = $this->dokuGetTokenB2B();

            if (!$tokenResult['success']) {
                throw new \RuntimeException('Failed to get B2B token: ' . ($tokenResult['error'] ?? 'Unknown error'));
            }

            $accessToken = $tokenResult['access_token'];

            // Get DOKU configuration
            $sandboxUrl = config('services.doku.api_url');
            $clientId = config('services.doku.client_id');
            $merchantId = config('services.doku.qris.merchant_id');

            if (empty($sandboxUrl) || empty($clientId) || empty($merchantId)) {
                throw new \RuntimeException('DOKU QRIS configuration is incomplete');
            }

            // Generate timestamp in ISO 8601 format with timezone (Asia/Jakarta)
            $timestamp = Carbon::now('Asia/Jakarta')->format('Y-m-d\TH:i:sP');

            // Generate validity period (30 minutes from now — matches t_transactions.expired_at)
            $validityPeriod = Carbon::now('Asia/Jakarta')->addMinutes(30)->format('Y-m-d\TH:i:sP');

            // Generate order_id / partner reference number
            $partnerReferenceNo = $data['order_id'];

            // Prepare request body
            $requestBody = [
                'partnerReferenceNo' => $partnerReferenceNo,
                'amount' => [
                    'value' => number_format($data['amount'], 2, '.', ''),
                    'currency' => 'IDR'
                ],
                'merchantId' => $merchantId,
                'terminalId' => 'A01',
                'validityPeriod' => $validityPeriod,
                'additionalInfo' => [
                    'postalCode' => '12220',
                    'feeType' => '1'
                ]
            ];

            // Generate signature for QRIS generation
            // For B2B2C APIs, signature uses HMAC-SHA512 with secret_key
            // Format: HTTPMethod:RelativeUrl:AccessToken:RequestBodyHash:Timestamp

            // Minify JSON (remove spaces, no pretty print)
            $jsonBody = json_encode($requestBody, JSON_UNESCAPED_SLASHES);

            // Calculate SHA256 hash of request body (lowercase hex string, NOT base64)
            $bodyHashHex = strtolower(hash('sha256', $jsonBody));

            // Build string to sign
            $httpMethod = 'POST';
            $relativeUrl = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';
            $stringToSign = "{$httpMethod}:{$relativeUrl}:{$accessToken}:{$bodyHashHex}:{$timestamp}";

            // Debug logging
            if (config('app.debug')) {
                \Log::debug('DOKU QRIS Signature Generation', [
                    'json_body' => $jsonBody,
                    'body_hash_hex' => $bodyHashHex,
                    'string_to_sign' => $stringToSign,
                    'timestamp' => $timestamp
                ]);
            }

            // Get secret key from config
            $secretKey = config('services.doku.secret_key');

            // Generate HMAC signature
            $signature = base64_encode(hash_hmac('sha512', $stringToSign, $secretKey, true));

            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$accessToken}",
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => $clientId,
                'X-EXTERNAL-ID' => $partnerReferenceNo,
                'CHANNEL-ID' => '95221'
            ];

            // Make API request
            $url = $sandboxUrl . $relativeUrl;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(
                fn($k, $v) => "{$k}: {$v}",
                array_keys($headers),
                $headers
            ));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \RuntimeException("CURL Error: {$curlError}");
            }

            $responseData = json_decode($response, true);

            if ($httpCode !== 200) {
                throw new \RuntimeException(
                    "DOKU API Error (HTTP {$httpCode}): " .
                    ($responseData['responseMessage'] ?? $response)
                );
            }

            // Check response code
            if (!isset($responseData['responseCode']) || $responseData['responseCode'] !== '2004700') {
                throw new \RuntimeException(
                    "QRIS Generation Failed: " .
                    ($responseData['responseMessage'] ?? 'Unknown error')
                );
            }

            return [
                'success' => true,
                'amount' => $data['amount'],
                'reference_no' => $responseData['referenceNo'],
                'partner_reference_no' => $responseData['partnerReferenceNo'],
                'qr_content' => $responseData['qrContent'],
                'terminal_id' => $responseData['terminalId'],
                'validity_period' => $responseData['additionalInfo']['validityPeriod'] ?? $validityPeriod,
                'response_code' => $responseData['responseCode'],
                'response_message' => $responseData['responseMessage'],
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            \Log::error('DOKU QRIS Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function testDokuGenerateCC(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'order_id' => 'nullable|string|max:255',
                'amount' => 'nullable|numeric|min:1',
                'customer_name' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email|max:255',
                'customer_phone' => 'nullable|string|max:20'
            ]);

            // Use provided values or defaults
            $orderId = $validated['order_id'] ?? 'TEST-' . time();
            $amount = $validated['amount'] ?? 100000;
            $customerName = $validated['customer_name'] ?? 'Test Customer';
            $customerEmail = $validated['customer_email'] ?? 'test@example.com';
            $customerPhone = $validated['customer_phone'] ?? '081234567890';

            $result = $this->dokuGenerateCC(
                orderId: $orderId,
                amount: $amount,
                customerName: $customerName,
                customerEmail: $customerEmail,
                customerPhone: $customerPhone
            );

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function dokuGenerateCC(
        string $orderId,
        float $amount,
        string $customerName,
        string $customerEmail,
        string $customerPhone
    ): array {
        try {
            // Get B2B access token first
            $tokenResult = $this->dokuGetTokenB2B();

            if (!$tokenResult['success']) {
                throw new \Exception('Failed to get access token: ' . $tokenResult['error']);
            }

            $accessToken = $tokenResult['access_token'];

            // Generate unique request-id (UUID v4)
            $requestId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // Generate timestamp in ISO 8601 format (UTC)
            $requestTimestamp = gmdate('Y-m-d\TH:i:s\Z');

            // Build request body
            $requestBody = [
                'order' => [
                    'invoice_number' => $orderId,
                    'amount' => $amount
                ],
                'customer' => [
                    'name' => $customerName,
                    'email' => $customerEmail,
                    'phone' => $this->normalizeDokuPhone($customerPhone)
                ],
                'override_configuration' => [
                    'themes' => [
                        'language' => 'ID',
                        'background_color' => '#FFFFFF',
                        'font_color' => '#000000',
                        'button_background_color' => '#0066CC',
                        'button_font_color' => '#FFFFFF'
                    ]
                ]
            ];

            // Minify JSON (remove spaces)
            $jsonBody = json_encode($requestBody, JSON_UNESCAPED_SLASHES);

            // Calculate SHA256 digest of request body (base64 encoded)
            $digestSHA256 = hash('sha256', $jsonBody, true); // true = raw binary output
            $digestBase64 = base64_encode($digestSHA256);

            // Build signature components with newline separators
            $clientId = config('services.doku.client_id');
            $signatureComponents =
                "Client-Id:{$clientId}\n" .
                "Request-Id:{$requestId}\n" .
                "Request-Timestamp:{$requestTimestamp}\n" .
                "Request-Target:/credit-card/v1/payment-page\n" .
                "Digest:{$digestBase64}";

            // Generate HMAC-SHA256 signature
            $secretKey = config('services.doku.secret_key');
            $signatureHmac = hash_hmac('sha256', $signatureComponents, $secretKey, true);
            $signatureBase64 = base64_encode($signatureHmac);
            $signature = "HMACSHA256={$signatureBase64}";

            // Log signature details for debugging
            \Log::info('DOKU CC Signature Generation', [
                'request_id' => $requestId,
                'timestamp' => $requestTimestamp,
                'digest' => $digestBase64,
                'signature_components' => $signatureComponents,
                'signature' => $signature
            ]);

            // Make API request
            $baseUrl = config('services.doku.api_url');
            $url = $baseUrl . '/credit-card/v1/payment-page';

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonBody,
                CURLOPT_HTTPHEADER => [
                    'Client-Id: ' . $clientId,
                    'Request-Id: ' . $requestId,
                    'Request-Timestamp: ' . $requestTimestamp,
                    'Signature: ' . $signature,
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_VERBOSE => false
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception("cURL Error: {$curlError}");
            }

            $responseData = json_decode($response, true);

            \Log::info('DOKU CC API Response', [
                'http_code' => $httpCode,
                'response' => $responseData
            ]);

            // Check for error response
            if ($httpCode !== 200 && $httpCode !== 201) {
                $errorMessage = $responseData['message'] ?? 'Unknown error';
                $errorDetails = isset($responseData['error']) ? json_encode($responseData['error']) : '';

                throw new \Exception(
                    "DOKU API Error (HTTP {$httpCode}): {$errorMessage} {$errorDetails}"
                );
            }

            // Check if payment page URL exists (success indicator for CC API)
            if (!isset($responseData['credit_card_payment_page']['url'])) {
                throw new \Exception(
                    "CC Payment Page Generation Failed: Payment URL not found in response"
                );
            }

            return [
                'success' => true,
                'invoice_number' => $responseData['order']['invoice_number'] ?? $orderId,
                'payment_url' => $responseData['credit_card_payment_page']['url'],
                'order_id' => $orderId,
                'amount' => $amount,
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            \Log::error('DOKU CC Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Preview refund calculation for a booking cancellation (without cancelling).
     * Returns the refund breakdown so the user can decide before confirming.
     *
     * GET /api/v1/booking/{order_id}/cancel-preview
     */
    public function previewCancelRefund(Request $request, $order_id)
    {
        try {
            // Find the transaction by order_id
            $transaction = Transaction::where('order_id', $order_id)->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found',
                ], 404);
            }

            $status = strtolower($transaction->transaction_status);

            // For pending/waiting bookings, no refund needed — just show cancellation is free
            if (in_array($status, ['pending', 'waiting'])) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Booking can be cancelled without refund',
                    'data' => [
                        'order_id' => $order_id,
                        'transaction_status' => $status,
                        'refund' => null,
                    ],
                ]);
            }

            // Only paid bookings (not yet checked-in) can be cancelled with refund
            if ($status !== 'paid') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking cannot be cancelled. Status: ' . $status,
                ], 400);
            }

            // Check if already checked in
            $booking = Booking::where('order_id', $order_id)->where('status', '1')->first();
            if ($booking && !is_null($booking->check_in_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot cancel a booking that has already been checked in',
                ], 400);
            }

            // Calculate refund breakdown
            $refundService = new RefundCalculationService();
            $refundData = $refundService->calculate($transaction);

            return response()->json([
                'status' => 'success',
                'message' => 'Refund preview calculated',
                'data' => [
                    'order_id' => $order_id,
                    'transaction_status' => $status,
                    'refund' => $refundData,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel preview error: ' . $e->getMessage(), [
                'order_id' => $order_id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate refund preview',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel a booking and create a refund record (for paid bookings).
     * For pending/waiting bookings, simply cancels without refund.
     *
     * POST /api/v1/booking/{order_id}/cancel
     */
    public function cancelBooking(Request $request, $order_id)
    {
        try {
            // Find the transaction by order_id
            $transaction = Transaction::where('order_id', $order_id)->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not found',
                ], 404);
            }

            $status = strtolower($transaction->transaction_status);

            // Validate the booking is cancellable
            if (!in_array($status, ['pending', 'waiting', 'paid'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking cannot be cancelled. Status: ' . $status,
                ], 400);
            }

            // For paid bookings, check not already checked in
            if ($status === 'paid') {
                $booking = Booking::where('order_id', $order_id)->where('status', '1')->first();
                if ($booking && !is_null($booking->check_in_at)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot cancel a booking that has already been checked in',
                    ], 400);
                }
            }

            $refundData = null;

            DB::beginTransaction();

            // For paid bookings: calculate refund and create refund record
            if ($status === 'paid') {
                $refundService = new RefundCalculationService();
                $refundData = $refundService->calculate($transaction);

                // If QRIS/VA, validate bank account details are provided
                if ($refundData['requires_bank_account']) {
                    $validator = Validator::make($request->all(), [
                        'bank_name' => 'required|string|max:100',
                        'account_no' => 'required|string|max:50',
                        'account_holder' => 'required|string|max:100',
                    ]);

                    if ($validator->fails()) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Bank account details are required for QRIS/VA refunds',
                            'errors' => $validator->errors(),
                        ], 422);
                    }
                }

                // Create refund record with breakdown
                Refund::create([
                    'id_booking' => $order_id,
                    'status' => 'pending',
                    'reason' => $request->input('reason', 'User-initiated cancellation'),
                    'amount' => $refundData['total_refund'],
                    'refund_type' => 'user',
                    'requested_by' => $transaction->user_id,
                    'room_refund' => $refundData['room_refund'],
                    'deposit_refund' => $refundData['deposit_refund'],
                    'other_refund' => $refundData['other_refund'],
                    // Bank account for QRIS/VA refunds
                    'refund_bank_name' => $request->input('bank_name'),
                    'refund_account_no' => $request->input('account_no'),
                    'refund_account_holder' => $request->input('account_holder'),
                ]);
            }

            // Restore voucher usage count if a voucher was applied
            if ($transaction->voucher_id) {
                $voucher = \App\Models\Voucher::find($transaction->voucher_id);
                if ($voucher && $voucher->current_usage_count > 0) {
                    $voucher->decrement('current_usage_count');
                }
            }

            // Update transaction status to cancelled
            DB::table('t_transactions')
                ->where('order_id', $order_id)
                ->update([
                    'transaction_status' => 'cancelled',
                    'cancel_at' => now(),
                ]);

            // Renewal rollback — find the most recent previous PAID/CONFIRMED transaction
            // for the same room + user using idrec and created_at ordering (more reliable
            // than matching check_out = check_in which can fail on edge-case renewals).
            $txRoomId    = (int)    $transaction->getAttribute('room_id');
            $txUserId    = (int)    $transaction->getAttribute('user_id');
            $txIdrec     = (int)    $transaction->getAttribute('idrec');
            $txCreatedAt = (string) $transaction->getAttribute('created_at');

            if ($transaction->is_renewal == 1) {
                $previousTransaction = DB::table('t_transactions')
                    ->where('room_id', $txRoomId)
                    ->where('user_id', $txUserId)
                    ->where('idrec', '!=', $txIdrec)
                    ->whereRaw('UPPER(transaction_status) IN (?, ?)', ['PAID', 'CONFIRMED'])
                    ->where('created_at', '<', $txCreatedAt)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($previousTransaction) {
                    $prevIdrec   = (int) $previousTransaction->idrec;
                    $prevOrderId = (string) $previousTransaction->order_id;

                    /* Skip rollback if the parent has another successful
                       renewal (paid/confirmed/completed, is_renewal=1) that
                       is newer than this cancelled one. Cancelling one
                       attempt of a multi-attempt renewal must not undo the
                       state set by a later, paid renewal of the same parent. */
                    $hasNewerSuccessfulRenewal = DB::table('t_transactions')
                        ->where('room_id', $txRoomId)
                        ->where('user_id', $txUserId)
                        ->where('idrec', '!=', $txIdrec)
                        ->where('idrec', '!=', $prevIdrec)
                        ->where('is_renewal', 1)
                        ->whereRaw('LOWER(transaction_status) IN (?, ?, ?)', ['paid', 'confirmed', 'completed'])
                        ->where('created_at', '>', $previousTransaction->created_at)
                        ->exists();

                    if ($hasNewerSuccessfulRenewal) {
                        Log::info('Renewal rollback SKIPPED on cancellation — newer successful renewal exists', [
                            'cancelled_transaction_id' => $txIdrec,
                            'previous_transaction_id'  => $prevIdrec,
                            'previous_order_id'        => $prevOrderId,
                        ]);
                    } else {
                        // Rollback renewal_status so the previous booking can be renewed again
                        DB::table('t_transactions')
                            ->where('idrec', $prevIdrec)
                            ->update(['renewal_status' => 0]);

                        // Restore previous booking as active (clear the check_out_at set during renewal)
                        DB::table('t_booking')
                            ->where('order_id', $prevOrderId)
                            ->update(['check_out_at' => null]);

                        /* rental_status untouched — physical occupancy is owned
                           by check-in / check-out only. */

                        Log::info('Renewal rollback on cancellation', [
                            'cancelled_transaction_id' => $txIdrec,
                            'previous_transaction_id'  => $prevIdrec,
                            'previous_order_id'        => $prevOrderId,
                        ]);
                    }
                }
            }

            // Deactivate the booking record
            DB::table('t_booking')
                ->where('order_id', $order_id)
                ->where('status', '1')
                ->update([
                    'status' => 0,
                    'reason' => 'User-initiated cancellation',
                ]);

            /* rental_status untouched — cancellation is disallowed after
               check-in, so the room was either already empty (flag stays 0)
               or someone else is occupying it (flag stays 1). Only check-out
               flips it to 0. */

            // Release parking quota
            if (!empty($transaction->parking_type)) {
                $this->decrementParkingQuota($transaction->property_id, $transaction->parking_type);
            }

            /**
             * Soft-delete the bundled-flow `t_parking` row tied to this order.
             *
             * Mirrors `ExpireBooking::handle`. Previously only the quota counter was
             * decremented; the actual `t_parking` row stayed active and only the daily
             * `parking:deactivate-expired` cron would eventually flip it. Soft-deleting
             * here releases the slot in lockstep with the cancellation.
             */
            $parkingRolledBack = DB::table('t_parking')
                ->where('order_id', $order_id)
                ->whereNull('deleted_at')
                ->update([
                    'status'     => 0,
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($parkingRolledBack > 0) {
                Log::info('Parking soft-deleted on booking cancellation', [
                    'order_id'          => $order_id,
                    'rows_soft_deleted' => $parkingRolledBack,
                ]);
            }

            // Update payment status
            DB::table('t_payment')
                ->where('order_id', $order_id)
                ->update(['payment_status' => 'cancelled']);

            DB::commit();

            // Send push notifications (non-blocking — failures don't affect the response)
            try {
                $firebaseService = new FirebaseNotificationService();

                $notifTitle = 'Booking Dibatalkan';
                $notifBody = "Booking {$order_id} telah dibatalkan.";
                if ($refundData) {
                    $notifBody .= " Refund: Rp " . number_format($refundData['total_refund'], 0, ',', '.');
                }
                $notifData = [
                    'type' => 'booking_cancelled',
                    'order_id' => $order_id,
                ];

                // Notify the guest — sendToUser expects a User model, not an int
                $guest = \App\Models\User::find($transaction->user_id);
                if ($guest) {
                    $firebaseService->sendToUser($guest, $notifTitle, $notifBody, $notifData);
                }

                // Notify admins of the property
                $adminTitle = 'Pembatalan Booking';
                $adminBody = "Booking {$order_id} ({$transaction->property_name}) dibatalkan oleh tamu.";
                $firebaseService->sendToAdmins($adminTitle, $adminBody, $notifData);

            } catch (\Exception $e) {
                Log::warning('Failed to send cancellation notification: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully',
                'data' => [
                    'order_id' => $order_id,
                    'refund' => $refundData,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel booking error: ' . $e->getMessage(), [
                'order_id' => $order_id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}