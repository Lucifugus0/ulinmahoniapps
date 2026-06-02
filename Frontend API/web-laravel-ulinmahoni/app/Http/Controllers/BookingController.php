<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\Property;
use App\Notifications\BookingConfirmationNotification;
use App\Jobs\ExpireBooking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check and expire pending bookings that have passed their expiration time
     * This runs automatically when users access their bookings
     *
     * @return void
     */
    private function checkAndExpireBookings()
    {
        // Safety-net only — the per-minute system cron (expire_transactions.sql)
        // remains the primary expiry driver. Throttle to at most one sweep per
        // 60s across the whole app so re-enabling this on the booking-list
        // request path doesn't run a full sweep on every request.
        if (! Cache::add('booking_expiry_sweep_lock', 1, 60)) {
            return;
        }

        try {
            $now = now();

            // Same selection as the deployed expire_transactions.sql:
            //  - pending
            //  - past deadline (expired_at < now) OR stale (transaction_date < now - 1h)
            //  - skip if another pending txn on the same room is still within its window
            $expiredTransactions = Transaction::where('transaction_status', 'pending')
                ->where(function ($q) use ($now) {
                    $q->where(function ($q2) use ($now) {
                            $q2->whereNotNull('expired_at')
                               ->where('expired_at', '<', $now);
                        })
                      ->orWhere('transaction_date', '<', $now->copy()->subHour());
                })
                ->whereNotExists(function ($sub) use ($now) {
                    $sub->selectRaw('1')
                        ->from('t_transactions as t2')
                        ->whereColumn('t2.room_id', 't_transactions.room_id')
                        ->whereColumn('t2.idrec', '!=', 't_transactions.idrec')
                        ->whereRaw("UPPER(t2.transaction_status) = 'PENDING'")
                        ->where(function ($w) use ($now) {
                            $w->whereNull('t2.expired_at')
                              ->orWhere('t2.expired_at', '>=', $now);
                        });
                })
                ->get();

            if ($expiredTransactions->isEmpty()) {
                return;
            }

            // Delegate each order to the canonical ExpireBooking routine.
            // dispatchSync runs handle() in-process regardless of QUEUE_CONNECTION,
            // giving the full consistent flow: bundled-parking soft-delete, voucher
            // restore, GUARDED renewal parent-rollback, push notifications, and —
            // exactly like the SQL — NO m_rooms.rental_status mutation. handle()
            // re-checks pending + payment, so it is idempotent if the row changed
            // between selection and dispatch.
            foreach ($expiredTransactions as $transaction) {
                try {
                    ExpireBooking::dispatchSync($transaction->order_id);
                } catch (\Exception $e) {
                    Log::error("Fallback expiry failed for order_id: {$transaction->order_id}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error in checkAndExpireBookings", [
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

    /**
     * Decrement parking quota when a booking changes parking type
     *
     * @param int $propertyId
     * @param string|null $parkingType
     * @return void
     */
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
            Log::info("Decremented parking quota", [
                'property_id' => $propertyId,
                'parking_type' => $parkingType,
                'new_quota_used' => $parkingFee->quota_used
            ]);
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
     * Check room availability
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRoomAvailability( string $roomId, string $checkIn, string $checkOut)
    {
        // Validate the parameters directly since we're not using a Request object
        $validator = \Validator::make([
            // 'property_id' => $propertyId,
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut
        ], [
            'property_id' => 'required|integer|exists:m_properties,idrec',
            'room_id' => 'required|integer|exists:m_rooms,idrec',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // $property_id = $propertyId;
        $room_id = $roomId;
        $check_in = Carbon::parse($checkIn)->startOfDay();
        $check_out = Carbon::parse($checkOut)->endOfDay();

        // Check for conflicting bookings using simplified query
        $conflictingBookings = DB::table('t_transactions')
            // ->where('property_id', $property_id)
            ->where('room_id', $room_id)
            ->where('status', '1')  // Active/confirmed booking
            ->whereNotIn('transaction_status', ['cancelled','expired'])
            ->where('check_in', '<', $check_out)
            ->where('check_out', '>', $check_in)
            ->limit(5)
            ->get();

        $isAvailable = $conflictingBookings->isEmpty();

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_available' => $isAvailable,
                'conflicting_bookings' => $isAvailable ? [] : $conflictingBookings,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                // 'room_id' => $roomId
                // 'property_id' => $propertyId,
            ]
        ]);
    }
    
    /**
     * Upload attachment for a booking
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function uploadAttachment(Request $request, $id)
    {
        try {
            $request->validate([
                'attachment_file' => 'required|file|mimes:jpg,jpeg,png|max:10240', // 10MB max
            ]);

            // Find the booking
            $booking = DB::table('t_transactions')
                ->where('idrec', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$booking) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Booking not found.'
                    ], 404);
                }
                return back()->with('error', 'Booking not found.');
            }

            // Handle file upload
            if ($request->hasFile('attachment_file') && $request->file('attachment_file')->isValid()) {
                $file = $request->file('attachment_file');
                
                // Validate file type
                $validTypes = ['image/jpeg', 'image/png'];
                if (!in_array($file->getMimeType(), $validTypes)) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid file type. Please upload a JPEG or PNG image.'
                        ], 400);
                    }
                    return back()->with('error', 'Invalid file type. Please upload a JPEG or PNG image.');
                }

                // Validate file size (10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'File is too large. Maximum size is 10MB.'
                        ], 400);
                    }
                    return back()->with('error', 'File is too large. Maximum size is 10MB.');
                }

                // Read file contents and convert to base64
                $fileContents = file_get_contents($file->getRealPath());
                $base64 = base64_encode($fileContents);

                // Update the booking with the new attachment
                $updated = DB::table('t_transactions')
                    ->where('idrec', $id)
                    ->update([
                        'attachment' => $base64,
                        'transaction_status' => 'waiting',
                        'updated_at' => now(),
                    ]);

                if ($updated) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Payment proof uploaded successfully.',
                            'data' => [
                                'booking_id' => $id,
                                'has_attachment' => true
                            ]
                        ]);
                    }
                    return back()->with('success', 'Payment proof uploaded successfully.');
                }


                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to update booking with attachment.'
                    ], 500);
                }
                return back()->with('error', 'Failed to update booking with attachment.');

            } else {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid file upload.'
                    ], 400);
                }
                return back()->with('error', 'Invalid file upload.');
            }
        } catch (\Exception $e) {
            \Log::error('Error uploading attachment: ' . $e->getMessage(), [
                'booking_id' => $id,
                'user_id' => Auth::id(),
                'exception' => $e
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while uploading the file.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            return back()->with('error', 'An error occurred while uploading the file. Please try again.');
        }
    }


    /**
     * View booking attachment
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Generate a signed URL for viewing the attachment
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateAttachmentUrl($id)
    {
        // Verify the booking exists and belongs to the user
        $booking = DB::table('t_transactions')
            ->where('idrec', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking || !$booking->attachment) {
            abort(404, 'Attachment not found');
        }

        // Create a signed URL that's valid for 30 minutes
        $signedUrl = URL::temporarySignedRoute(
            'attachments.view',
            now()->addMinutes(10),
            ['id' => $id]
        );

        return redirect($signedUrl);
    }

    /**
     * View booking attachment (protected by signed URL)
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function viewAttachment($id)
    {
        // The signed middleware already verified the request
        $booking = DB::table('t_transactions')
            ->where('idrec', $id)
            ->first();

        if (!$booking || !$booking->attachment) {
            abort(404, 'Attachment not found');
        }

        // Detect MIME type
        $mime = 'image/png'; // default
        $decoded = base64_decode($booking->attachment, true);
        if ($decoded !== false && strlen($decoded) > 2) {
            if (substr($decoded, 0, 2) === "\xFF\xD8") $mime = 'image/jpeg';
            elseif (substr($decoded, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") $mime = 'image/png';
        }

        return response($decoded)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="booking_attachment_' . $id . '"')
            ->header('Cache-Control', 'private, max-age=1800'); // Cache for 30 minutes (matching URL expiration)
    }

    /**
     * Update payment method for a booking
     * 
     * @param Request $request
     * @param string $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_method' => 'required',
                'order_id' => 'nullable|string',
                'virtual_account_no' => 'nullable|string',
                'bank' => 'nullable|string',
                'va_data' => 'nullable|string',
                'deposit_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'parking_type' => 'nullable|string',
                'parking_duration' => 'nullable|integer|min:0',
                'voucher_code' => 'nullable|string',
                'discount_amount' => 'nullable|numeric|min:0',
                'vehicle_plate' => 'nullable|string|max:20',
                'owner_name' => 'nullable|string|max:100',
                'owner_phone' => 'nullable|string|max:20'
            ]);

            $booking = DB::table('t_transactions')
                ->where('idrec', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$booking) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
                }
                return back()->with('error', 'Booking not found.');
            }

            // Prepare update data
            $updateData = [
                'transaction_type' => $request->payment_method,
                'transaction_status' => 'pending',
                'paid_at' => null,
                'updated_at' => now()
            ];

            // Add order_id if provided
            if ($request->has('order_id')) {
                $updateData['order_id'] = $request->order_id;
            }

            // Add virtual account number if provided
            if ($request->has('virtual_account_no')) {
                $updateData['virtual_account_no'] = $request->virtual_account_no;
            }

            // Add bank if provided
            if ($request->has('bank')) {
                $updateData['payment_bank'] = $request->bank;
            }

            // Handle deposit fee
            $depositFee = floatval($booking->deposit_fee ?? 0);
            if ($request->has('deposit_fee')) {
                $depositFee = floatval($request->deposit_fee);
                $updateData['deposit_fee'] = $depositFee;
            }

            // Handle parking fee, type, and duration
            $parkingFee = floatval($booking->parking_fee ?? 0);
            if ($request->has('parking_fee')) {
                $parkingFee = floatval($request->parking_fee);
                $updateData['parking_fee'] = $parkingFee;
            }
            if ($request->has('parking_type') && $request->parking_type !== 'none') {
                $updateData['parking_type'] = $request->parking_type;
            }
            if ($request->has('parking_duration')) {
                $updateData['parking_duration'] = intval($request->parking_duration);
            }

            // Handle voucher discount
            $discountAmount = floatval($booking->discount_amount ?? 0);
            if ($request->has('voucher_code')) {
                $updateData['voucher_code'] = $request->voucher_code;
            }
            if ($request->has('discount_amount')) {
                $discountAmount = floatval($request->discount_amount);
                $updateData['discount_amount'] = $discountAmount;
            }

            // Recalculate grandtotal with all fees
            // Formula: Grandtotal = (room_price + admin_fees) - discount + service_fees + deposit + parking
            $roomPrice = floatval($booking->room_price ?? 0);
            $adminFees = floatval($booking->admin_fees ?? 0);
            $serviceFees = floatval($booking->service_fees ?? 0);

            $subtotal = $roomPrice + $adminFees;
            $newGrandtotal = $subtotal - $discountAmount + $serviceFees + $depositFee + $parkingFee;

            $updateData['grandtotal_price'] = $newGrandtotal;
            $updateData['subtotal_before_discount'] = $subtotal;

            DB::table('t_transactions')
                ->where('idrec', $id)
                ->update($updateData);

            // Also update t_payment grandtotal_price
            DB::table('t_payment')
                ->where('order_id', $booking->order_id)
                ->update(['grandtotal_price' => $newGrandtotal]);

            // Handle parking quota based on renewal status
            if ($parkingFee > 0 && $request->parking_type && $request->parking_type !== 'none') {
                $user = Auth::user();

                // Compute the parking rent period (start = check_in, end capped at check_out).
                // Booked-with-room parking always starts at the booking's check-in date.
                $parkingMonths = intval($request->parking_duration ?? 1);
                $startRent = null;
                $endRent = null;
                if (!empty($booking->check_in)) {
                    $startCarbon = \Carbon\Carbon::parse($booking->check_in)->startOfDay();
                    $endCarbon = $startCarbon->copy()->addMonths($parkingMonths);
                    if (!empty($booking->check_out)) {
                        $checkOutCarbon = \Carbon\Carbon::parse($booking->check_out)->startOfDay();
                        if ($endCarbon->gt($checkOutCarbon)) {
                            $endCarbon = $checkOutCarbon;
                        }
                    }
                    $startRent = $startCarbon->toDateString();
                    $endRent = $endCarbon->toDateString();
                }

                /**
                 * Always INSERT a new t_parking row for this paid period (multi-row design,
                 * see CLAUDE.md "Parking System / Storage: one row per paid period").
                 *
                 * The prior-parking lookup runs **regardless of `is_renewal`** — a customer
                 * can keep using their slot across non-renewal bookings (consecutive month-
                 * to-month bookings, fresh booking after a previous stay still in chain).
                 * Quota decisions are made off this lookup, not off the booking flag:
                 *   - existing same type   → no quota change (slot already counted)
                 *   - existing other type  → swap (decrement old, increment new)
                 *   - no existing row      → fresh increment
                 * Plate / owner / phone fall back to the prior row when the request omits them.
                 */
                $existingParking = DB::table('t_parking')
                    ->where('user_id', Auth::id())
                    ->where('property_id', $booking->property_id)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderByDesc('idrec')
                    ->first();

                if ($existingParking && $existingParking->parking_type !== $request->parking_type) {
                    $this->decrementParkingQuota($booking->property_id, $existingParking->parking_type);
                    $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                } elseif (!$existingParking) {
                    $this->incrementParkingQuota($booking->property_id, $request->parking_type);
                }
                // else: same-type renewal — quota unchanged, slot already counted

                try {
                    DB::table('t_parking')->insert([
                        'property_id' => $booking->property_id,
                        'parking_type' => $request->parking_type,
                        'vehicle_plate' => $request->vehicle_plate ?? ($existingParking->vehicle_plate ?? null),
                        'owner_name' => $request->owner_name ?? ($existingParking->owner_name ?? $user->name ?? null),
                        'owner_phone' => $request->owner_phone ?? ($existingParking->owner_phone ?? $user->phone_number ?? null),
                        'user_id' => Auth::id(),
                        'parking_duration' => intval($request->parking_duration ?? 1),
                        'start_rent' => $startRent,
                        'end_rent' => $endRent,
                        'fee_amount' => $parkingFee,
                        'order_id' => $booking->order_id,
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

            // Log voucher usage if voucher was applied
            if ($request->has('voucher_code') && $request->discount_amount > 0) {
                try {
                    $voucherService = app(\App\Services\VoucherService::class);

                    // Find the voucher by code
                    $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();

                    if ($voucher) {
                        // Increment usage count
                        $voucher->increment('current_usage_count');

                        // Calculate amounts for logging
                        // Original amount is the grandtotal BEFORE discount was applied
                        $originalAmount = $booking->grandtotal_price; // This is still the old value (before DB update above)
                        $discountAmount = $request->discount_amount;
                        $finalAmount = $originalAmount - $discountAmount;

                        // Log usage
                        $voucherService->logUsage([
                            'voucher_id' => $voucher->idrec,
                            'voucher_code' => $request->voucher_code,
                            'user_id' => Auth::id(),
                            'order_id' => $booking->order_id,
                            'transaction_id' => $booking->idrec,
                            'property_id' => $booking->property_id,
                            'room_id' => $booking->room_id,
                            'original_amount' => $originalAmount,
                            'discount_amount' => $discountAmount,
                            'final_amount' => $finalAmount
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Voucher usage logging failed: ' . $e->getMessage());
                    // Continue even if logging fails
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment method updated successfully.',
                    'redirect_url' => route('bookings.index')
                ]);
            }
            return redirect()->route('bookings.index')
                ->with('success', 'Payment method updated successfully.');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating payment method: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error updating payment method: ' . $e->getMessage());
        }
    }

    public function index()
    {
        // Auto-expire pending bookings when user accesses their bookings
        // (throttled safety-net; system cron remains the primary driver)
        $this->checkAndExpireBookings();

        $tab = request()->get('tab', 'all');
        $userId = Auth::id();

        // Debug: Log the tab and user ID
        \Log::info('Bookings index', [
            'tab' => $tab,
            'user_id' => $userId,
            'url' => request()->fullUrl()
        ]);

        // Get all bookings for the user with relationships.
        // Sorted by t_transactions.created_at desc so the most recently created transaction
        // (e.g. a fresh renewal or a new booking) surfaces at the top of every tab — matches
        // the new "Tanggal Pemesanan" row admins/guests see in the ID Pemesanan cell.
        $bookings = Transaction::with(['user', 'room', 'property', 'booking'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Debug: Log the query results
        \Log::info('All user bookings', [
            'count' => $bookings->count(),
            'statuses' => $bookings->pluck('transaction_status')->toArray()
        ]);
        
        // Get counts for tabs
        $completedCount = $bookings->whereIn('transaction_status', values: ['paid', 'completed'])->count();
        $activeCount = $bookings->whereNotIn('transaction_status', ['pending','paid', 'completed', 'cancelled'])->count();
        $cancelledCount = $bookings->where('transaction_status', 'cancelled')->count();

        return view('bookings.index', [
            'allBookings' => $bookings,  // Pass all bookings to the view
            'completedCount' => $completedCount,
            'activeCount' => $activeCount,
            'cancelledCount' => $cancelledCount,
            'activeTab' => $tab
        ]);
    }

    public function store(Request $request)
    {
        $response = ['success' => false, 'message' => '', 'redirect_url' => null];

        $validator = \Validator::make($request->all(), [
            'rent_type' => 'required|in:daily,monthly',
            // Daily: check-in max 90 days from now. Monthly: max 14 days from now.
            'check_in' => [
                'nullable', 'date', 'after_or_equal:today',
                'before_or_equal:' . ($request->rent_type === 'monthly' ? now()->addDays(14)->format('Y-m-d') : now()->addDays(90)->format('Y-m-d')),
            ],
            'check_out' => 'nullable|date|after:check_in',
            'property_name' => 'required|string',
            'room_name' => 'nullable|string',
            'room_id' => 'required|integer|exists:m_rooms,idrec',
            'booking_type' => 'nullable|string|max:100',
            'months' => 'nullable|integer',
            'voucher_code' => 'nullable|string|min:8|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to make a booking.',
                    'redirect_url' => route('login')
                ], 401);
            }

            // Get room with property relationship
            $room = Room::with('property')->findOrFail($request->room_id);

            // Verify room name matches (prevent tampering)
            if ($room->name !== $request->room_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room information mismatch',
                    'errors' => ['room_name' => ['The room information does not match.']]
                ], 422);
            }

            // Get the price based on rent type
            /* Multi-Tier Pricing: support 'annual' rent_type alongside daily/monthly */
            $priceField = 'price_original_' . $request->rent_type;
            $price = $room->$priceField;

            if (!$price || $price <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This room is not available for booking at the moment. No valid price found.',
                    'errors' => [
                        'price' => ["No valid {$request->rent_type} rate found for this room."]
                    ]
                ], 400);
            }

            // CHECK BOOKING AVAILABILITY BEFORE PROCEEDING
            try {
                $this->checkRoomAvailability( $request->room_id, $request->check_in, $request->check_out);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['availability' => [$e->getMessage()]]
                ], 409);
            }

            // Calculate dates and prices
            // Parse dates first without time for accurate day calculation
            $checkInDate = Carbon::parse($request->check_in);
            $checkOutDate = Carbon::parse($request->check_out);

            /* Multi-Tier Pricing: Calculate booking duration and total price */
            $bookingMonths = null;
            $bookingDays = null;
            $bookingYears = null;
            $priceBreakdown = null;

            if ($request->rent_type === 'monthly') {
                $bookingMonths = (int) $request->months;
                // Clamped month addition: if target month has fewer days, clamp to last day
                // e.g. Jan 31 + 1 month = Feb 28, Mar 31 + 1 month = Apr 30
                $checkOutDate = $checkInDate->copy();
                $targetMonth = $checkOutDate->month + $bookingMonths;
                $targetYear = $checkOutDate->year + intdiv($targetMonth - 1, 12);
                $targetMonth = (($targetMonth - 1) % 12) + 1;
                $maxDay = Carbon::create($targetYear, $targetMonth, 1)->daysInMonth;
                $checkOutDate = Carbon::create($targetYear, $targetMonth, min($checkInDate->day, $maxDay), $checkInDate->hour, $checkInDate->minute, $checkInDate->second);
                $totalPrice = $price * $bookingMonths;

            } elseif ($request->rent_type === 'annual') {
                /* Multi-Tier Pricing: Annual booking — flat rate × years */
                $bookingYears = (int) ($request->years ?? 1);
                $checkOutDate = $checkInDate->copy()->addYears($bookingYears);
                $totalPrice = $price * $bookingYears;

            } else {
                /* Multi-Tier Pricing: Daily booking — sum per-date prices from m_room_prices */
                $bookingDays = $checkInDate->diffInDays($checkOutDate);

                $datePrices = \DB::table('m_room_prices')
                    ->where('room_id', $room->idrec)
                    ->where('date', '>=', $checkInDate->toDateString())
                    ->where('date', '<', $checkOutDate->toDateString())
                    ->where('status', 1)
                    ->orderBy('date')
                    ->get(['date', 'price', 'price_type']);

                if ($datePrices->isNotEmpty()) {
                    /* Use per-date resolved prices (weekday/weekend/holiday/season) */
                    $totalPrice = $datePrices->sum('price');
                    $priceBreakdown = $datePrices->map(function ($p) {
                        $date = \Carbon\Carbon::parse($p->date);
                        return [
                            'date' => $p->date,
                            'price' => (float) $p->price,
                            'type' => $p->price_type ?? 'weekday',
                            'day_name' => $date->format('l'),
                        ];
                    })->values()->toArray();
                    $bookingDays = count($priceBreakdown);
                } else {
                    /* Fallback: no per-date prices — use flat rate (backward compat) */
                    $totalPrice = $price * $bookingDays;
                }
            }

            // Now set the proper check-in and check-out times for the transaction
            // Set check-in time to 14:00:00 and check-out time to 12:00:00
            $checkIn = $checkInDate->copy()->setTime(14, 0, 0);
            $checkOut = $checkOutDate->copy()->setTime(12, 0, 0);

            // $adminFee = $totalPrice * 0.1; // 10% admin fee
            $adminFee = 0;
            $serviceFees = 30000;
            $tax = 0;

            $grandtotalPrice = $totalPrice + $adminFee + $serviceFees + $tax;

            // Voucher processing
            $voucherId = null;
            $voucherCode = null;
            $discountAmount = 0;
            $subtotalBeforeDiscount = $grandtotalPrice;

            if ($request->filled('voucher_code')) {
                try {
                    $voucherService = app(\App\Services\VoucherService::class);

                    // Validate voucher
                    $voucherValidation = $voucherService->validateVoucher(
                        $request->voucher_code,
                        $user->id,
                        $grandtotalPrice,
                        $room->property_id,
                        $room->idrec
                    );

                    if (!$voucherValidation['valid']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Voucher validation failed',
                            'errors' => ['voucher' => $voucherValidation['errors']]
                        ], 422);
                    }

                    $voucher = $voucherValidation['voucher'];
                    $calculation = $voucherService->calculateDiscount($voucher, $grandtotalPrice);

                    $voucherId = $voucher->idrec;
                    $voucherCode = $voucher->voucher;
                    $discountAmount = $calculation['discount_amount'];
                    $grandtotalPrice = $calculation['final_amount'];

                    // Increment voucher usage count
                    $voucher->increment('current_usage_count');

                } catch (\Exception $e) {
                    \Log::error('Voucher processing failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher processing error: ' . $e->getMessage(),
                        'errors' => ['voucher' => [$e->getMessage()]]
                    ], 422);
                }
            }

            // Generate unique order_id in format UMH-yymmddXXXPP
            $propertyInitial = $room->property->initial ?? 'HX';
            do {
                $randomNumber = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
                $order_id = 'UMH-' . now()->format('ymd') . $randomNumber . $propertyInitial;
            } while (Transaction::where('order_id', $order_id)->exists());

            // Set expiration time to 30 minutes from now
            $expiredAt = now()->addMinutes(30);

            // Prepare transaction data
            $transactionData = [
                'property_id' => $room->property_id,
                'room_id' => $room->idrec,
                // 'room_id' => $request->room_id,
                'order_id' => $order_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone_number' => $user->phone_number,
                'property_name' => $room->property->name ?? $request->property_name,
                'transaction_date' => now(),
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'original_checkin_day' => Carbon::parse($checkIn)->day,
                'room_name' => $room->name,
                'booking_type' => $request->booking_type,
                'booking_days' => $bookingDays,
                'booking_months' => $bookingMonths,
                'booking_years' => $bookingYears,     /* Multi-Tier Pricing */
                'daily_price' => $request->rent_type === 'daily' ? $price : null,
                'monthly_price'=> $request->rent_type === 'monthly' ? $price : null,
                'annual_price' => $request->rent_type === 'annual' ? $price : null, /* Multi-Tier Pricing */
                'room_price' => $totalPrice,
                'admin_fees' => $adminFee,
                'service_fees' => $serviceFees,
                'tax' => $tax,
                'grandtotal_price' => $grandtotalPrice,
                'voucher_id' => $voucherId,
                'voucher_code' => $voucherCode,
                'discount_amount' => $discountAmount,
                'subtotal_before_discount' => $subtotalBeforeDiscount,
                'price_breakdown' => $priceBreakdown ? json_encode($priceBreakdown) : null, /* Multi-Tier Pricing: per-date price breakdown */
                'property_type' => $room->type ?? $request->property_type ?? 'room',
                'transaction_code' => 'TRX-' . strtoupper(Str::random(16)),
                'transaction_status' => 'pending',
                'status' => '1',
                'expired_at' => $expiredAt,
            ];

            // Create transaction
            $transaction = Transaction::create($transactionData);

            // Log voucher usage if voucher was applied
            if ($voucherId && $voucherCode) {
                try {
                    $voucherService = app(\App\Services\VoucherService::class);
                    $voucherService->logUsage([
                        'voucher_id' => $voucherId,
                        'voucher_code' => $voucherCode,
                        'user_id' => $user->id,
                        'order_id' => $order_id,
                        'transaction_id' => $transaction->idrec,
                        'property_id' => $room->property_id,
                        'room_id' => $room->idrec,
                        'original_amount' => $subtotalBeforeDiscount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $grandtotalPrice
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Voucher usage logging failed: ' . $e->getMessage());
                    // Continue even if logging fails
                }
            }

            // Create booking
            $bookingData = [
                'property_id' => $room->property_id,
                'order_id' => $order_id,
                'room_id' => $room->idrec,
                // 'check_in_at' => $checkIn,
                // 'check_out_at' => $checkOut,
                'status' => '1',
                'booking_type' => $request->booking_type
            ];
            $booking = Booking::create($bookingData);

            // Create payment record
            $paymentData = [
                'property_id' => $room->property_id,
                'room_id' => $room->idrec,
                'order_id' => $order_id,
                'user_id' => $user->id,
                'grandtotal_price' => $grandtotalPrice,
                'payment_status' => 'unpaid',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            Payment::create($paymentData);

            // Booking will be automatically expired by scheduled task if not paid within 1 hour
            Log::info("Booking created with expiration time: {$expiredAt} for order_id: {$order_id}");

            /* m_rooms.rental_status untouched — physical occupancy flag is
               owned exclusively by check-in / check-out. */

            // Process payment with DOKU
            try {
                // Use the already calculated grandtotalPrice (with voucher discount applied if any)
                
                // $dokuPaymentResponse = $this->processDokuPayment([
                //     'order_id' => $order_id,
                //     'transaction_code' => $transaction->transaction_code,
                //     'amount' => $grandtotalPrice,
                //     'property_name' => $property->name ?? 'Property',
                //     'room_name' => $room->name ?? 'Room',
                //     'user_name' => $user->name ?? 'Customer',
                //     'user_email' => $user->email,
                //     'user_phone' => $user->phone ?? $request->user_phone_number ?? '0000000000',
                //     'user_address' => $user->address ?? 'Indonesia'
                // ]);

                // Update transaction with payment URL if available
                if (!empty($dokuPaymentResponse['payment_url'])) {
                    $transaction->update([
                        'payment_url' => $dokuPaymentResponse['payment_url'] ?? null,
                        'payment_expired_at' => $dokuPaymentResponse['expired_at'] ?? now()->addHours(1)
                    ]);
                }

                DB::commit();

                // Send booking confirmation email
                $paymentUrl = $dokuPaymentResponse['payment_url'] ?? route('payment.show', ['booking' => $transaction->order_id]);
                $this->sendEmailBooking($user, $bookingData, $transactionData, $paymentUrl);

                // return response()->json([
                //     'success' => true,
                //     'message' => 'Booking created successfully',
                //     'redirect_url' => $paymentResponse['payment_url'] ?? route('payment.show', ['booking' => $transaction->order_id]),
                //     'payment_data' => $paymentResponse
                // ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'payment_options' => [
                        'doku' => $dokuPaymentResponse['payment_url'] ?? null,
                        'other' => route('payment.show', ['booking' => $transaction->order_id]),
                    ],
                    'payment_data' => $dokuPaymentResponse ?? []
                ]);


            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Payment processing failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'order_id' => $order_id,
                    'user_id' => $user->id ?? null
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Booking created but payment processing failed: ' . $e->getMessage(),
                    'redirect_url' => route('payment.show', ['booking' => $transaction->order_id])
                ], 500);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'The requested room is not available.',
                'errors' => ['room_id' => ['The requested room is not available.']]
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'room_id' => $request->room_id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to create booking',
                'errors' => ['server' => [$e->getMessage() ?: 'An unexpected error occurred']]
            ], 500);
        }
    }

    /**
     * Mark a booking as expired
     *
     * @param string $id Booking ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function markExpired($id)
    {
        try {
            // Find the booking with related data
            $booking = Booking::with(['user', 'room', 'property'])
                ->where('idrec', $id)
                ->firstOrFail();

            // Check if the booking belongs to the authenticated user
            if (auth()->id() !== $booking->user_id) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not authorized to update this booking.'
                    ], 403);
                }
                return back()->with('error', 'You are not authorized to update this booking.');
            }

            // Check if booking is still pending
            if ($booking->transaction_status !== 'pending') {
                $message = 'Only pending bookings can be marked as expired.';
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $message
                    ], 400);
                }
                return back()->with('error', $message);
            }

            // Update the status to expired
            $booking->update([
                'transaction_status' => 'expired',
                'updated_at' => now()
            ]);

            // Log the action
            Log::info("Booking #{$booking->idrec} marked as expired by user #" . auth()->id());

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Booking has been marked as expired.',
                    'data' => [
                        'booking_id' => $booking->idrec,
                        'status' => 'expired'
                    ]
                ]);
            }

            return back()->with('success', 'Booking has been marked as expired.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $message = 'Booking not found.';
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], 404);
            }
            return back()->with('error', $message);
        } catch (\Exception $e) {
            Log::error('Error marking booking as expired: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while processing your request.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while processing your request.');
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
     * Process payment through Doku payment gateway
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
            // $dokuUrl = config('services.doku.api_url', 'https://api-sandbox.doku.com/checkout/v1/payment');
            // $clientId = config('services.doku.client_id');
            // $secretKey = config('services.doku.secret_key');
            $dokuUrl = env('DOKU_CHECKOUT_URL') . '/checkout/v1/payment';
            $clientId = env('DOKU_CHECKOUT_CLIENT_ID');
            $secretKey = env('DOKU_CHECKOUT_SECRET_KEY');
            
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
            $requestId = (string) \Illuminate\Support\Str::uuid();
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
            $response = \Illuminate\Support\Facades\Http::timeout(30)
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
                
                \Illuminate\Support\Facades\Log::error('Doku API Request Failed', $errorDetails);
                throw new \RuntimeException($errorDetails['message'], $errorDetails['code'] ?? 0);
            }

            return [
                'payment_url' => $responseData['response']['payment']['url'] ?? null,
                'expired_at' => $responseData['response']['payment']['expired_datetime'] ?? null,
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Processing Error', [
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
}
