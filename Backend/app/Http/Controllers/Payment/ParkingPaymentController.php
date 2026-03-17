<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParkingFeeTransaction;
use App\Models\ParkingFeeTransactionImage;
use App\Models\ParkingFee;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParkingPaymentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = ParkingFeeTransaction::with(['property', 'parking', 'images', 'verifiedBy', 'createdBy', 'transaction'])
            ->orderBy('created_at', 'desc');

        $user = Auth::user();
        if ($user->isSite() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('order_id', 'like', "%{$request->search}%")
                    ->orWhere('user_name', 'like', "%{$request->search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('transaction_status', $request->status);
        }

        if ($request->has('parking_type') && !empty($request->parking_type)) {
            $query->where('parking_type', $request->parking_type);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $parkingTransactions = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.payment.parking.partials.parking-payment_table', compact('parkingTransactions'));
        }

        return view('pages.payment.parking.index', compact('parkingTransactions'));
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = ParkingFeeTransaction::with(['property', 'parking', 'images', 'verifiedBy', 'createdBy', 'transaction'])
            ->orderBy('created_at', 'desc');

        $user = Auth::user();
        if ($user->isSite() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        if (!empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('order_id', 'like', "%{$request->search}%")
                    ->orWhere('user_name', 'like', "%{$request->search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$request->search}%");
            });
        }

        if (!empty($request->status)) {
            $query->where('transaction_status', $request->status);
        }

        if (!empty($request->parking_type)) {
            $query->where('parking_type', $request->parking_type);
        }

        if (!empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if (!empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $parkingTransactions = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.payment.parking.partials.parking-payment_table', compact('parkingTransactions'))->render(),
            'pagination' => $perPage !== 'all' && $parkingTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $parkingTransactions->links()->toHtml()
                : ''
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $transaction = ParkingFeeTransaction::findOrFail($id);

            $transaction->update([
                'transaction_status' => 'paid',
                'paid_at' => now(),
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran parkir berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            $transaction = ParkingFeeTransaction::with('parking')->findOrFail($id);

            // Store previous status to check if we need to decrement quota
            $wasPaid = $transaction->transaction_status === 'paid';

            // Check if user has other active paid parking (renewal) before rejecting
            $hasOtherActivePaid = ParkingFeeTransaction::where('user_id', $transaction->user_id)
                ->where('property_id', $transaction->property_id)
                ->where('parking_type', $transaction->parking_type)
                ->where('transaction_status', 'paid')
                ->where('idrec', '!=', $transaction->idrec)
                ->exists();

            $transaction->update([
                'transaction_status' => 'rejected',
                'notes' => $request->notes,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Only decrement quota if was paid AND user has no other active paid parking (not a renewal)
            if ($wasPaid && !$hasOtherActivePaid) {
                $parkingFee = $transaction->getParkingFeeViaParking();
                if ($parkingFee && $parkingFee->capacity > 0) {
                    $parkingFee->decrementQuota();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran parkir berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewProof($id)
    {
        $transaction = ParkingFeeTransaction::with('images')->findOrFail($id);

        $images = $transaction->images->where('status', 1);

        if ($images->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada bukti pembayaran'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'images' => $images->map(function ($img) {
                return [
                    'id' => $img->idrec,
                    'image_url' => asset('storage/' . $img->image),
                    'image_type' => $img->image_type,
                    'description' => $img->description,
                ];
            })->values()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'parking_type' => 'required|in:car,motorcycle',
            'vehicle_plate' => 'required|string|max:50',
            'parking_duration' => 'required|integer|min:1',
            'fee_amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_proof' => 'required|image|mimes:jpeg,jpg|max:5120', // 5MB in kilobytes
            'notes' => 'nullable|string|max:1000',
        ], [
            'order_id.required' => 'Please select a booking order',
            'order_id.exists' => 'Selected booking order not found',
            'payment_proof.image' => 'Payment proof must be an image file',
            'payment_proof.mimes' => 'Payment proof must be a JPG/JPEG file',
            'payment_proof.max' => 'Payment proof size must not exceed 5MB',
        ]);

        try {
            DB::beginTransaction();

            // Get transaction details from order_id
            $bookingTransaction = \App\Models\Transaction::where('order_id', $request->order_id)->firstOrFail();

            // Validate parking_duration does not exceed stay duration
            if ($bookingTransaction->check_in && $bookingTransaction->check_out) {
                $checkIn = \Carbon\Carbon::parse($bookingTransaction->check_in);
                $checkOut = \Carbon\Carbon::parse($bookingTransaction->check_out);
                $maxMonths = $checkIn->diffInMonths($checkOut);
                if ($checkIn->copy()->addMonths($maxMonths)->lt($checkOut)) {
                    $maxMonths++;
                }
                $maxMonths = max(1, $maxMonths);

                if ($request->parking_duration > $maxMonths) {
                    throw new \Exception(
                        "Parking duration ({$request->parking_duration} months) cannot exceed stay duration ({$maxMonths} months). " .
                        "Stay period: {$checkIn->format('d M Y')} - {$checkOut->format('d M Y')}."
                    );
                }
            }

            // Check if this order already has a parking transaction for the same vehicle plate
            $existingActiveParking = ParkingFeeTransaction::where('order_id', $request->order_id)
                ->where('vehicle_plate', strtoupper($request->vehicle_plate))
                ->where('transaction_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->first();

            // $isExtension = true when customer extends expired parking within the SAME order
            // (slot is still occupied, quota must NOT be incremented again)
            $isExtension = false;
            $extensionOldParkingType = null;

            if ($existingActiveParking) {
                // Calculate expiry: transaction_date + parking_duration months
                $expiryDate = \Carbon\Carbon::parse($existingActiveParking->transaction_date)
                    ->addMonths($existingActiveParking->parking_duration);

                if ($expiryDate->isFuture()) {
                    $typeLabel = ucfirst($existingActiveParking->parking_type);
                    throw new \Exception(
                        "Kendaraan {$existingActiveParking->vehicle_plate} pada order {$request->order_id} sudah memiliki parkir aktif ({$typeLabel}) " .
                        "hingga {$expiryDate->format('d M Y')}."
                    );
                }

                // Parking expired but customer still checked in → same-order extension
                $isExtension = true;
                $extensionOldParkingType = $existingActiveParking->parking_type;
            }

            // Get property details
            $property = \App\Models\Property::findOrFail($bookingTransaction->property_id);

            // Generate property initials (first letter of each word)
            $propertyWords = explode(' ', $property->name);
            $propertyInitials = '';
            foreach ($propertyWords as $word) {
                if (!empty($word)) {
                    $propertyInitials .= strtoupper(substr($word, 0, 1));
                }
            }
            if (strlen($propertyInitials) > 3) {
                $propertyInitials = substr($propertyInitials, 0, 3);
            }

            // Get current month and year
            $transactionDate = \Carbon\Carbon::parse($request->transaction_date);
            $month = $transactionDate->month;
            $year = $transactionDate->year;

            // Convert month to Roman numeral
            $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romanMonths[$month - 1];

            // Get sequential number for this month/year/property
            $lastTransaction = ParkingFeeTransaction::where('property_id', $bookingTransaction->property_id)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->orderBy('idrec', 'desc')
                ->first();

            $sequentialNumber = 1;
            if ($lastTransaction && $lastTransaction->invoice_id) {
                // Extract number from last invoice_id (format: 0001/PRK-XX/KGA-INV/I/2026)
                preg_match('/^(\d+)\//', $lastTransaction->invoice_id, $matches);
                if (!empty($matches[1])) {
                    $sequentialNumber = intval($matches[1]) + 1;
                }
            }

            // Format sequential number with leading zeros
            $formattedNumber = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

            // Generate invoice ID: 0001/PRK-XX/KGA-INV/I/2026
            $invoiceId = sprintf(
                '%s/PRK-%s/KGA-INV/%s/%s',
                $formattedNumber,
                $propertyInitials,
                $romanMonth,
                $year
            );

            // Get parking_fee - MUST exist in Parking Fee Management
            $parkingFee = ParkingFee::where('property_id', $bookingTransaction->property_id)
                ->where('parking_type', $request->parking_type)
                ->where('status', 1)
                ->first();

            // Parking fee MUST be configured in Parking Fee Management first
            if (!$parkingFee) {
                throw new \Exception(
                    'Parking fee for ' . ucfirst($request->parking_type) . ' is not configured for this property. ' .
                    'Please create parking fee in Parking Fee Management first before creating parking payment.'
                );
            }

            // Find or create parking registration in t_parking
            $parking = Parking::withTrashed()
                ->where('property_id', $bookingTransaction->property_id)
                ->where('vehicle_plate', strtoupper($request->vehicle_plate))
                ->first();

            // Cek apakah quota sudah dikonsumsi oleh Parking Management (management_only=1)
            // Jika ya, PP tidak perlu increment quota lagi (slot sudah terpakai)
            $quotaAlreadyConsumedByManagement = false;

            if ($parking && $parking->trashed()) {
                $parking->restore();
                // Restored = treat as new, quota perlu di-increment
                $quotaAlreadyConsumedByManagement = false;
            } elseif ($parking && $parking->management_only) {
                // Kendaraan sebelumnya didaftarkan via Parking Management (tanpa invoice)
                // Quota sudah dikonsumsi oleh PM → PP tidak increment lagi
                // PP mengambil alih pengelolaan quota → reset flag management_only
                $quotaAlreadyConsumedByManagement = true;
                $parking->update(['management_only' => 0, 'updated_by' => Auth::id()]);
            }

            if (!$parking) {
                $parking = Parking::create([
                    'property_id' => $bookingTransaction->property_id,
                    'parking_type' => $request->parking_type,
                    'vehicle_plate' => strtoupper($request->vehicle_plate),
                    'owner_name' => $bookingTransaction->user_name,
                    'owner_phone' => $bookingTransaction->user_phone_number,
                    'user_id' => $bookingTransaction->user_id,
                    'order_id' => $request->order_id,
                    'status' => 1,
                    'management_only' => 0,
                    'created_by' => Auth::id(),
                ]);
            }

            // Determine if this is a renewal booking (user extending their stay, same vehicle/slot)
            $isRenewalBooking = $bookingTransaction->is_renewal == 1;
            $existingParkingType = null; // Tipe parkir lama yang dimiliki user (jika ada)

            if ($isRenewalBooking) {
                // Cek 1: dari t_parking_fee_transaction (Alur PP - beli via Parking Payment)
                $prevParkingTxn = ParkingFeeTransaction::where('user_id', $bookingTransaction->user_id)
                    ->where('property_id', $bookingTransaction->property_id)
                    ->where('transaction_status', 'paid')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($prevParkingTxn) {
                    $existingParkingType = $prevParkingTxn->parking_type;
                }

                // Cek 2: dari t_transactions order sebelumnya (Alur booking kamar + parkir)
                if (!$existingParkingType) {
                    $prevTxn = \App\Models\Transaction::where('user_id', $bookingTransaction->user_id)
                        ->where('property_id', $bookingTransaction->property_id)
                        ->where('transaction_status', 'paid')
                        ->whereNotNull('parking_type')
                        ->where('parking_fee', '>', 0)
                        ->whereNotNull('parking_duration')
                        ->where('order_id', '!=', $request->order_id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($prevTxn) {
                        $existingParkingType = $prevTxn->parking_type;
                    }
                }

                // Cek 3: dari t_parking langsung (Parking Management atau record aktif)
                if (!$existingParkingType) {
                    $existingParkingRecord = Parking::where('user_id', $bookingTransaction->user_id)
                        ->where('property_id', $bookingTransaction->property_id)
                        ->where('status', 1)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($existingParkingRecord) {
                        $existingParkingType = $existingParkingRecord->parking_type;
                    }
                }
            }

            // User sudah punya parkir sebelumnya → ini renewal sesungguhnya
            $isRenewal = $isRenewalBooking && !is_null($existingParkingType);

            // Tipe parkir berubah saat renewal (misal motor → mobil)?
            // Jika ya: bebaskan kuota tipe lama, ambil kuota tipe baru
            $isParkingTypeChanged = $isRenewal && ($existingParkingType !== $request->parking_type);

            // Update order_id pada record t_parking agar selalu menunjuk ke order terbaru
            if ($isRenewal && $parking && $parking->order_id !== $request->order_id) {
                $parking->update([
                    'order_id' => $request->order_id,
                    'updated_by' => Auth::id(),
                ]);
            }

            // Cek ketersediaan kuota:
            // - Parkir baru atau renewal ganti tipe → cek kuota tipe baru
            // - Renewal tipe sama → skip (slot sudah occupied sebelumnya)
            if (!$isRenewal && !$quotaAlreadyConsumedByManagement) {
                if ($parkingFee->capacity > 0 && !$parkingFee->hasAvailableQuota()) {
                    throw new \Exception(
                        'Parking quota is full for ' . ucfirst($request->parking_type) .
                        '. Available: 0, Capacity: ' . $parkingFee->capacity .
                        '. Please wait for check-out or increase capacity in Parking Fee Management.'
                    );
                }
            } elseif ($isParkingTypeChanged) {
                if ($parkingFee->capacity > 0 && !$parkingFee->hasAvailableQuota()) {
                    throw new \Exception(
                        'Parking quota is full for ' . ucfirst($request->parking_type) .
                        '. Tidak dapat mengubah tipe parkir dari ' . ucfirst($existingParkingType) .
                        ' ke ' . ucfirst($request->parking_type) .
                        '. Available: 0, Capacity: ' . $parkingFee->capacity . '.'
                    );
                }
            }

            // Create parking fee transaction with status 'paid' and already verified
            $transaction = ParkingFeeTransaction::create([
                'property_id' => $bookingTransaction->property_id,
                'parking_id' => $parking->idrec,
                'invoice_id' => $invoiceId,
                'order_id' => $request->order_id,
                'user_id' => $bookingTransaction->user_id,
                'user_name' => $bookingTransaction->user_name,
                'user_phone' => $bookingTransaction->user_phone_number,
                'parking_type' => $request->parking_type,
                'vehicle_plate' => strtoupper($request->vehicle_plate),
                'parking_duration' => $request->parking_duration,
                'fee_amount' => $request->fee_amount,
                'transaction_date' => $request->transaction_date,
                'transaction_status' => 'paid', // Already paid
                'paid_at' => now(),
                'verified_by' => Auth::id(), // Already verified
                'verified_at' => now(),
                'notes' => $request->notes,
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            // Handle image upload - Save as file
            if ($request->hasFile('payment_proof')) {
                $image = $request->file('payment_proof');

                // Create directory if not exists
                $uploadPath = storage_path('app/public/parking_payments');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Generate unique filename (sanitize invoiceId by replacing / with -)
                $sanitizedInvoiceId = str_replace('/', '-', $invoiceId);
                $filename = $sanitizedInvoiceId . '_' . time() . '.' . $image->getClientOriginalExtension();

                // Store the file
                $image->move($uploadPath, $filename);

                // Save file path to database
                ParkingFeeTransactionImage::create([
                    'parking_transaction_id' => $transaction->idrec,
                    'image' => 'parking_payments/' . $filename, // Store relative path
                    'image_type' => $image->getClientOriginalExtension(),
                    'description' => 'Payment proof uploaded by admin',
                    'status' => 1,
                    'created_by' => Auth::id(),
                ]);
            }

            // Kelola kuota parkir berdasarkan skenario:
            // - Parkir baru                → increment tipe baru
            // - Extension tipe sama        → tidak ada perubahan (slot sudah occupied)
            // - Extension ganti tipe       → decrement tipe lama, increment tipe baru
            // - Renewal tipe sama          → tidak ada perubahan (slot sudah occupied)
            // - Renewal ganti tipe         → decrement tipe lama, increment tipe baru
            // - Sudah dikonsumsi PM        → tidak increment (slot sudah tercatat)
            $extensionTypeChanged = $isExtension && ($extensionOldParkingType !== $request->parking_type);

            if (($isRenewal && $isParkingTypeChanged) || $extensionTypeChanged) {
                // Bebaskan kuota tipe lama
                $oldType = $isRenewal ? $existingParkingType : $extensionOldParkingType;
                $oldParkingFee = ParkingFee::where('property_id', $bookingTransaction->property_id)
                    ->where('parking_type', $oldType)
                    ->where('status', 1)
                    ->first();

                if ($oldParkingFee && $oldParkingFee->capacity > 0) {
                    $oldParkingFee->decrementQuota();
                }

                // Ambil kuota tipe baru
                if ($parkingFee->capacity > 0) {
                    $parkingFee->incrementQuota();
                }
            } elseif (!$isRenewal && !$isExtension && !$quotaAlreadyConsumedByManagement && $parkingFee->capacity > 0) {
                $parkingFee->incrementQuota();
            }

            DB::commit();

            // Build success message
            if ($isRenewal && $isParkingTypeChanged) {
                $message = 'Parking renewal payment added successfully (tipe parkir berubah: '
                    . ucfirst($existingParkingType) . ' → ' . ucfirst($request->parking_type) . ')';
            } elseif ($isRenewal) {
                $message = 'Parking renewal payment added successfully (quota unchanged)';
            } elseif ($extensionTypeChanged) {
                $message = 'Parking extension payment added successfully (tipe parkir berubah: '
                    . ucfirst($extensionOldParkingType) . ' → ' . ucfirst($request->parking_type) . ')';
            } elseif ($isExtension) {
                $message = 'Parking extension payment added successfully (quota unchanged)';
            } elseif ($quotaAlreadyConsumedByManagement) {
                $message = 'Parking payment added successfully (quota already counted by Parking Management)';
            } else {
                $message = 'Parking payment added successfully';
            }
            if ($parkingFee->capacity === 0) {
                $message .= '. This property has unlimited parking (no quota limit).';
            } elseif ($isParkingTypeChanged || $extensionTypeChanged || (!$isRenewal && !$isExtension && !$quotaAlreadyConsumedByManagement)) {
                $remaining = $parkingFee->fresh()->available_quota;
                $message .= ". Parking quota: {$remaining}/{$parkingFee->capacity} available.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $transaction,
                'parking_info' => [
                    'capacity' => $parkingFee->capacity,
                    'quota_used' => $parkingFee->quota_used,
                    'available_quota' => $parkingFee->available_quota,
                    'is_unlimited' => $parkingFee->capacity === 0,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add parking payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getParkingFees($propertyId)
    {
        try {
            $parkingFees = ParkingFee::where('property_id', $propertyId)
                ->where('status', 1)
                ->get(['idrec', 'parking_type', 'fee', 'capacity', 'quota_used'])
                ->map(function($fee) {
                    return [
                        'idrec' => $fee->idrec,
                        'parking_type' => $fee->parking_type,
                        'fee' => $fee->fee,
                        'capacity' => $fee->capacity,
                        'quota_used' => $fee->quota_used,
                        'available_quota' => $fee->available_quota,
                        'quota_percentage' => $fee->quota_usage_percentage,
                    ];
                });

            return response()->json($parkingFees);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load parking fees: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCheckedInOrders(Request $request)
    {
        try {
            $propertyId = $request->get('property_id');

            // Query dari t_booking dengan join ke t_transactions
            $query = \App\Models\Booking::whereNotNull('check_in_at')
                ->whereNull('check_out_at') // Masih check-in, belum check-out
                ->where('status', 1) // Active booking
                ->with(['transaction' => function($q) {
                    $q->where('transaction_status', 'paid')
                        ->where('status', 1);
                }, 'property', 'room']);

            // Filter by property if user is site admin
            $user = Auth::user();
            if ($user->isSite() && $user->property_id) {
                $query->where('property_id', $user->property_id);
            } elseif ($propertyId) {
                $query->where('property_id', $propertyId);
            }

            $orders = $query->orderBy('check_in_at', 'desc')
                ->get()
                ->filter(function($booking) {
                    // Filter only bookings with paid transaction
                    return $booking->transaction && $booking->transaction->transaction_status === 'paid';
                })
                ->map(function($booking) {
                    // Calculate max parking duration in months based on stay period
                    $maxParkingMonths = null;
                    if ($booking->transaction && $booking->transaction->check_in && $booking->transaction->check_out) {
                        $checkIn = \Carbon\Carbon::parse($booking->transaction->check_in);
                        $checkOut = \Carbon\Carbon::parse($booking->transaction->check_out);
                        $maxParkingMonths = $checkIn->diffInMonths($checkOut);
                        // If there are remaining days beyond full months, round up
                        if ($checkIn->copy()->addMonths($maxParkingMonths)->lt($checkOut)) {
                            $maxParkingMonths++;
                        }
                        // Minimum 1 month
                        $maxParkingMonths = max(1, $maxParkingMonths);
                    }

                    $isRenewalBooking = $booking->transaction && $booking->transaction->is_renewal == 1;
                    $userId = $booking->transaction->user_id ?? null;
                    $parkingStatus = 'new';
                    $parkingInfo = null;

                    // === Alur 2: Cek t_parking_fee_transaction by order_id saat ini ===
                    $existingParking = ParkingFeeTransaction::where('order_id', $booking->order_id)
                        ->where('transaction_status', 'paid')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($existingParking) {
                        $expiryDate = \Carbon\Carbon::parse($existingParking->transaction_date)
                            ->addMonths($existingParking->parking_duration ?? 1);
                        $parkingStatus = $expiryDate->isFuture() ? 'active' : 'renewal';
                        $parkingInfo = [
                            'parking_type'  => $existingParking->parking_type,
                            'vehicle_plate' => $existingParking->vehicle_plate,
                            'duration'      => $existingParking->parking_duration,
                            'expiry_date'   => $expiryDate->format('d M Y'),
                            'expired_ago'   => $expiryDate->diffForHumans(),
                        ];
                    }

                    // === Alur 1: Cek t_transactions — parkir dibeli bersamaan booking kamar ===
                    if (!$parkingInfo) {
                        $txn = $booking->transaction;
                        if ($txn && $txn->parking_type && $txn->parking_fee > 0 && $txn->parking_duration) {
                            $checkIn = \Carbon\Carbon::parse($txn->check_in);
                            $expiryDate = $checkIn->copy()->addMonths((int) $txn->parking_duration);
                            $parkingStatus = $expiryDate->isFuture() ? 'active' : 'renewal';

                            // Ambil plat kendaraan dari t_parking berdasarkan user + properti + tipe
                            $parkingRecord = \App\Models\Parking::where('user_id', $userId)
                                ->where('property_id', $booking->property_id)
                                ->where('parking_type', $txn->parking_type)
                                ->where('status', 1)
                                ->first();

                            $parkingInfo = [
                                'parking_type'  => $txn->parking_type,
                                'vehicle_plate' => $parkingRecord->vehicle_plate ?? null,
                                'duration'      => $txn->parking_duration,
                                'expiry_date'   => $expiryDate->format('d M Y'),
                                'expired_ago'   => $expiryDate->diffForHumans(),
                            ];
                        }
                    }

                    // === Renewal: Cek t_parking_fee_transaction order sebelumnya (Alur 2 lama) ===
                    if (!$parkingInfo && $isRenewalBooking && $userId) {
                        $prevParkingTxn = ParkingFeeTransaction::where('user_id', $userId)
                            ->where('property_id', $booking->property_id)
                            ->where('transaction_status', 'paid')
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($prevParkingTxn) {
                            $expiryDate = \Carbon\Carbon::parse($prevParkingTxn->transaction_date)
                                ->addMonths($prevParkingTxn->parking_duration ?? 1);
                            $parkingStatus = 'renewal';
                            $parkingInfo = [
                                'parking_type'  => $prevParkingTxn->parking_type,
                                'vehicle_plate' => $prevParkingTxn->vehicle_plate,
                                'duration'      => $prevParkingTxn->parking_duration,
                                'expiry_date'   => $expiryDate->format('d M Y'),
                                'expired_ago'   => $expiryDate->diffForHumans(),
                            ];
                        }
                    }

                    // === Renewal: Cek t_transactions order sebelumnya (Alur 1 lama) ===
                    if (!$parkingInfo && $isRenewalBooking && $userId) {
                        $prevTxn = \App\Models\Transaction::where('user_id', $userId)
                            ->where('property_id', $booking->property_id)
                            ->where('transaction_status', 'paid')
                            ->whereNotNull('parking_type')
                            ->where('parking_fee', '>', 0)
                            ->whereNotNull('parking_duration')
                            ->where('order_id', '!=', $booking->order_id)
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($prevTxn && $prevTxn->check_in && $prevTxn->parking_duration) {
                            $checkIn = \Carbon\Carbon::parse($prevTxn->check_in);
                            $expiryDate = $checkIn->copy()->addMonths((int) $prevTxn->parking_duration);
                            $parkingStatus = 'renewal';

                            // Ambil plat kendaraan dari t_parking berdasarkan user + properti + tipe
                            $parkingRecord = \App\Models\Parking::where('user_id', $userId)
                                ->where('property_id', $booking->property_id)
                                ->where('parking_type', $prevTxn->parking_type)
                                ->where('status', 1)
                                ->first();

                            $parkingInfo = [
                                'parking_type'  => $prevTxn->parking_type,
                                'vehicle_plate' => $parkingRecord->vehicle_plate ?? null,
                                'duration'      => $prevTxn->parking_duration,
                                'expiry_date'   => $expiryDate->format('d M Y'),
                                'expired_ago'   => $expiryDate->diffForHumans(),
                            ];
                        }
                    }

                    // Fetch all active parking records for this user at this property
                    $activeParkings = [];
                    if ($userId) {
                        $activeParkingRecords = \App\Models\Parking::where('user_id', $userId)
                            ->where('property_id', $booking->property_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get(['idrec', 'vehicle_plate', 'parking_type']);

                        foreach ($activeParkingRecords as $ap) {
                            // Get latest paid transaction for this parking record to determine duration/expiry
                            $latestTxn = ParkingFeeTransaction::where('parking_id', $ap->idrec)
                                ->where('transaction_status', 'paid')
                                ->orderBy('created_at', 'desc')
                                ->first();

                            $apExpiryDate = null;
                            $apIsExpired  = null;
                            $apDuration   = null;

                            if ($latestTxn) {
                                $apExpiry    = \Carbon\Carbon::parse($latestTxn->transaction_date)
                                    ->addMonths($latestTxn->parking_duration ?? 1);
                                $apExpiryDate = $apExpiry->format('d M Y');
                                $apIsExpired  = !$apExpiry->isFuture();
                                $apDuration   = $latestTxn->parking_duration;
                            }

                            $activeParkings[] = [
                                'vehicle_plate'    => $ap->vehicle_plate,
                                'parking_type'     => $ap->parking_type,
                                'parking_duration' => $apDuration,
                                'expiry_date'      => $apExpiryDate,
                                'is_expired'       => $apIsExpired,
                            ];
                        }
                    }

                    return [
                        'order_id' => $booking->order_id,
                        'property_id' => $booking->property_id,
                        'user_name' => $booking->transaction->user_name ?? $booking->user_name,
                        'user_phone' => $booking->transaction->user_phone_number ?? $booking->user_phone_number,
                        'room_name' => $booking->room->name ?? '-',
                        'property_name' => $booking->property->name ?? '-',
                        'check_in' => $booking->transaction && $booking->transaction->check_in
                            ? $booking->transaction->check_in->format('d M Y')
                            : '',
                        'check_out' => $booking->transaction && $booking->transaction->check_out
                            ? $booking->transaction->check_out->format('d M Y')
                            : '-',
                        'max_parking_months' => $maxParkingMonths,
                        'parking_status' => $parkingStatus,
                        'parking_info'   => $parkingInfo,
                        'active_parkings' => $activeParkings,
                        'display_text' => $booking->order_id . ' - ' . ($booking->transaction->user_name ?? $booking->user_name) . ' (' . ($booking->room->name ?? '-') . ')',
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load checked-in orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check out parking - Release quota
     */
    public function checkout(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transaction = ParkingFeeTransaction::with('parking')->findOrFail($id);

            // Only paid transactions can be checked out
            if ($transaction->transaction_status !== 'paid') {
                throw new \Exception('Only paid parking can be checked out');
            }

            // Cek apakah user masih punya parkir aktif lain (dari semua sumber)
            // Jika ya → jangan kembalikan kuota (slot masih terpakai)

            // Cek 1: t_parking_fee_transaction lain yang masih paid (Alur PP)
            $hasOtherActivePaid = ParkingFeeTransaction::where('user_id', $transaction->user_id)
                ->where('property_id', $transaction->property_id)
                ->where('parking_type', $transaction->parking_type)
                ->where('transaction_status', 'paid')
                ->where('idrec', '!=', $transaction->idrec)
                ->exists();

            // Cek 2: order lain di t_transactions yang punya parkir bundled (Alur booking kamar + parkir)
            // Misal: user memperpanjang kamar dan parkir sudah termasuk di booking baru
            if (!$hasOtherActivePaid) {
                $hasOtherActivePaid = \App\Models\Transaction::where('user_id', $transaction->user_id)
                    ->where('property_id', $transaction->property_id)
                    ->where('transaction_status', 'paid')
                    ->where('parking_type', $transaction->parking_type)
                    ->where('parking_fee', '>', 0)
                    ->whereNotNull('parking_duration')
                    ->where('order_id', '!=', $transaction->order_id)
                    ->exists();
            }

            // Cek 3: t_parking masih aktif via Parking Management (management_only=1)
            // Berarti slot sudah dikonsumsi PM, PP tidak boleh kembalikan kuota PM
            if (!$hasOtherActivePaid) {
                $hasOtherActivePaid = \App\Models\Parking::where('user_id', $transaction->user_id)
                    ->where('property_id', $transaction->property_id)
                    ->where('parking_type', $transaction->parking_type)
                    ->where('status', 1)
                    ->where('management_only', 1)
                    ->exists();
            }

            // Update transaction to completed/checked-out
            $transaction->update([
                'transaction_status' => 'completed',
                'updated_by' => Auth::id(),
            ]);

            // Kembalikan kuota hanya jika user tidak punya parkir aktif lain
            if (!$hasOtherActivePaid) {
                $parkingFee = $transaction->getParkingFeeViaParking();
                if ($parkingFee && $parkingFee->capacity > 0) {
                    $parkingFee->decrementQuota();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Parking checked out successfully. Quota has been released.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to checkout parking: ' . $e->getMessage()
            ], 500);
        }
    }
}
