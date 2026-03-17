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

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

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
        $validator = \Validator::make($request->all(), [
            'property_id' => 'required|integer|exists:m_properties,idrec',
            'room_id' => 'required|integer|exists:m_rooms,idrec',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'is_renewal' => 'nullable|integer|in:0,1',
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
        $checkIn = Carbon::parse($request->check_in)->startOfDay();
        $checkOut = Carbon::parse($request->check_out)->endOfDay();
        $isRenewal = $request->is_renewal == 1;

        // Check if room rental_status is 1 (already rented)
        // Skip this check if is_renewal = 1 (renewals are allowed for currently rented rooms)
        $room = DB::table('m_rooms')->where('idrec', $roomId)->first();
        if (!$isRenewal && $room && $room->rental_status == 1) {
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

        
        // NEW CODE - with t_booking join to get checked_in_at and checked_out_at
        $conflictingBookings = DB::table('t_transactions')
            ->join('t_booking', 't_booking.order_id', '=', 't_transactions.order_id')
            ->where('t_transactions.property_id', $propertyId)
            ->where('t_transactions.room_id', $roomId)
            ->where('t_transactions.status', '1')
            ->whereNotIn('t_transactions.transaction_status', [
                'cancelled',
                'expired',
                'checked_out'
            ])
            ->whereNull('t_booking.check_out_at') // Exclude if guest has already checked out
            ->where('t_transactions.check_in', '<', $checkOut)
            ->where('t_transactions.check_out', '>', $checkIn)
            ->select(
                't_transactions.*',
                't_booking.check_in_at',
                't_booking.check_out_at'
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
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'daily_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'booking_days' => 'nullable|integer|required_without:booking_months',
            'booking_months' => 'nullable|integer|required_without:booking_days',
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

            // Set expiration time to 15 minutes from now
            $expiredAt = now()->addMinutes(15);

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

            // Update room rental_status to 1 (room is booked/rented)
            DB::table('m_rooms')
                ->where('idrec', $request->room_id)
                ->update(['rental_status' => 1]);

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
            'check_out' => 'required|date|after:check_in',
            // PRICING
            'daily_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'booking_days' => 'nullable|integer|min:0',
            'booking_months' => 'nullable|integer|min:0',
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

            // Set check-in time to 14:00:00 and check-out time to 12:00:00
            // Use createFromFormat to avoid timezone issues with date-only strings
            $checkInWithTime = Carbon::createFromFormat('Y-m-d', $request->check_in, config('app.timezone'))->setTime(14, 0, 0);
            $checkOutWithTime = Carbon::createFromFormat('Y-m-d', $request->check_out, config('app.timezone'))->setTime(12, 0, 0);

            // Calculate pricing based on booking type
            $bookingDays = null;
            $bookingMonths = null;
            $roomPrice = 0;

            // Determine booking type - prioritize explicit booking_type parameter
            $isDaily = $request->booking_type === 'daily';

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

            // Set expiration time to 15 minutes from now
            $expiredAt = now()->addMinutes(15);

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
                // ROOM DATA
                'room_id' => $request->room_id,
                'room_name' => $request->room_name,
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
                    'room_id' => $request->room_id,
                    'original_amount' => $subtotalBeforeDiscount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $grandtotalPrice
                ]);
            }

            // 1. Find old active booking (status=1) by order_id and update check_out_at
            $oldBooking = Booking::where('order_id', $orderId)->where('status', '1')->first();
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
                'room_id' => $request->room_id,
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

            // Ensure room rental_status stays 1 (room remains occupied by renewed booking)
            DB::table('m_rooms')
                ->where('idrec', $request->room_id)
                ->update(['rental_status' => 1]);

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

            // Insert parking record into t_parking table (only for new bookings with parking, not renewals)
            if ($parkingFee > 0 && $request->parking_type && $request->parking_type !== 'none') {
                try {
                    $user = Auth::user();
                    $existingParking = DB::table('t_parking')
                        ->where('order_id', $booking->order_id)
                        ->first();

                    if ($existingParking) {
                        // Update existing record if parking type changed
                        if ($existingParking->parking_type !== $request->parking_type) {
                            $this->decrementParkingQuota($booking->property_id, $existingParking->parking_type);
                            $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                        }
                        DB::table('t_parking')
                            ->where('idrec', $existingParking->idrec)
                            ->update([
                                'parking_type' => $request->parking_type,
                                'vehicle_plate' => $request->vehicle_plate ?? $existingParking->vehicle_plate,
                                'owner_name' => $request->owner_name ?? $existingParking->owner_name,
                                'owner_phone' => $request->owner_phone ?? $existingParking->owner_phone,
                                'parking_duration' => $parkingDuration ?: $existingParking->parking_duration,
                                'fee_amount' => $parkingFee,
                                'updated_at' => now(),
                            ]);
                    } else {
                        $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                        DB::table('t_parking')->insert([
                            'property_id' => $booking->property_id,
                            'order_id' => $booking->order_id,
                            'parking_type' => $request->parking_type,
                            'vehicle_plate' => $request->vehicle_plate ?? null,
                            'owner_name' => $request->owner_name ?? ($user->name ?? null),
                            'owner_phone' => $request->owner_phone ?? ($user->phone_number ?? null),
                            'user_id' => Auth::id(),
                            'parking_duration' => $parkingDuration,
                            'fee_amount' => $parkingFee,
                            'management_only' => 0,
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to insert/update parking record: ' . $e->getMessage());
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

            // Calculate expiration date (60 minutes from now)
            $expiredDate = Carbon::now('Asia/Jakarta')->addMinutes(60)->format('Y-m-d\TH:i:sP');

            $requestBody = [
                'partnerServiceId' => $partnerServiceId,
                'customerNo' => $customerNo,
                'virtualAccountNo' => $virtualAccountNo,
                'virtualAccountName' => $data['user_name'],
                'virtualAccountEmail' => $data['user_email'],
                'virtualAccountPhone' => $data['user_phone'],
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

            // Generate validity period (1 hour from now)
            $validityPeriod = Carbon::now('Asia/Jakarta')->addHour()->format('Y-m-d\TH:i:sP');

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
                    'phone' => $customerPhone
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
}