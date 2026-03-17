<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\ParkingFee;
use App\Models\ParkingFeeTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParkingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = Parking::with(['property', 'createdBy', 'updatedBy', 'bookingTransaction'])
            ->orderBy('property_id', 'asc');

        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            $query->where('property_id', $accessiblePropertyId);
        }

        // Include soft-deleted if requested
        if ($request->input('show_deleted') === '1') {
            $query->withTrashed();
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('vehicle_plate', 'like', "%{$request->search}%")
                  ->orWhere('owner_name', 'like', "%{$request->search}%")
                  ->orWhereHas('property', function ($q2) use ($request) {
                      $q2->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status !== '' && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('parking_type') && !empty($request->parking_type)) {
            $query->where('parking_type', $request->parking_type);
        }

        $parkings = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.Properties.Parking.partials.parking_table', compact('parkings'));
        }

        return view('pages.Properties.Parking.index', compact('parkings'));
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');
        $status = $request->input('status', '');
        $parkingType = $request->input('parking_type', '');
        $showDeleted = $request->input('show_deleted', '0');

        $query = Parking::with(['property', 'createdBy', 'updatedBy', 'bookingTransaction'])
            ->orderBy('property_id', 'asc');

        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            $query->where('property_id', $accessiblePropertyId);
        }

        if ($showDeleted === '1') {
            $query->withTrashed();
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_plate', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($parkingType)) {
            $query->where('parking_type', $parkingType);
        }

        $page = (int) $request->input('page', 1);
        $parkings = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage, ['*'], 'page', $page)->appends($request->except('page'));

        return response()->json([
            'html' => view('pages.Properties.Parking.partials.parking_table', compact('parkings'))->render(),
            'pagination' => $perPage !== 'all' && $parkings instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $parkings->links()->toHtml()
                : ''
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'parking_type' => 'required|in:car,motorcycle',
            'vehicle_plate' => 'required|string|max:50',
            'parking_duration' => 'nullable|integer|min:1',
            'fee_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Get transaction details from order_id
            $bookingTransaction = \App\Models\Transaction::where('order_id', $request->order_id)->firstOrFail();

            // Check if parking fee is configured for this property and type
            $parkingFee = ParkingFee::where('property_id', $bookingTransaction->property_id)
                ->where('parking_type', $request->parking_type)
                ->where('status', 1)
                ->first();

            if (!$parkingFee) {
                throw new \Exception(
                    'Parking fee for ' . ucfirst($request->parking_type) . ' is not configured for this property. ' .
                    'Please create parking fee in Parking Fee Management first.'
                );
            }

            // Determine if this is a renewal booking (user extending their stay, same vehicle/slot)
            $isRenewalBooking = $bookingTransaction->is_renewal == 1;

            // Skip quota check only when:
            // - Booking is a renewal (is_renewal=1) AND
            // - User already has active paid parking at this property (vehicle still occupies the spot)
            // Note: cek dari ParkingFeeTransaction (PP) ATAU dari t_parking management_only=1 milik user ini
            $isRenewal = $isRenewalBooking
                ? (
                    ParkingFeeTransaction::where('user_id', $bookingTransaction->user_id)
                        ->where('property_id', $bookingTransaction->property_id)
                        ->where('parking_type', $request->parking_type)
                        ->where('transaction_status', 'paid')
                        ->exists()
                    ||
                    Parking::where('user_id', $bookingTransaction->user_id)
                        ->where('property_id', $bookingTransaction->property_id)
                        ->where('parking_type', $request->parking_type)
                        ->where('management_only', 1)
                        ->where('status', 1)
                        ->exists()
                )
                : false;

            // Only check quota for new parking, not renewals (vehicle already occupies a spot)
            if (!$isRenewal) {
                if ($parkingFee->capacity > 0 && !$parkingFee->hasAvailableQuota()) {
                    throw new \Exception(
                        'Parking quota is full for ' . ucfirst($request->parking_type) .
                        '. Available: 0, Capacity: ' . $parkingFee->capacity .
                        '. Please wait for check-out or increase capacity in Parking Fee Management.'
                    );
                }
            }

            // Validate parking duration against stay duration
            if ($request->parking_duration && $bookingTransaction->check_in && $bookingTransaction->check_out) {
                $checkIn = \Carbon\Carbon::parse($bookingTransaction->check_in);
                $checkOut = \Carbon\Carbon::parse($bookingTransaction->check_out);
                $maxMonths = $checkIn->diffInMonths($checkOut);
                if ($checkIn->copy()->addMonths($maxMonths)->lt($checkOut)) {
                    $maxMonths++;
                }
                $maxMonths = max(1, $maxMonths);

                if ($request->parking_duration > $maxMonths) {
                    throw new \Exception(
                        "Parking duration ({$request->parking_duration} months) cannot exceed stay duration ({$maxMonths} months)."
                    );
                }
            }

            $vehiclePlate = strtoupper($request->vehicle_plate);

            // Check if vehicle plate already exists for this property
            $existing = Parking::withTrashed()
                ->where('property_id', $bookingTransaction->property_id)
                ->where('vehicle_plate', $vehiclePlate)
                ->first();

            if ($existing && !$existing->trashed()) {
                if ($isRenewalBooking) {
                    // Renewal: update record lama dengan order_id baru dan data terbaru
                    $existing->update([
                        'parking_type' => $request->parking_type,
                        'owner_name' => $bookingTransaction->user_name,
                        'owner_phone' => $bookingTransaction->user_phone_number,
                        'user_id' => $bookingTransaction->user_id,
                        'order_id' => $request->order_id,
                        'parking_duration' => $request->parking_duration,
                        'fee_amount' => $request->fee_amount,
                        'notes' => $request->notes,
                        'status' => 1,
                        'management_only' => 1,
                        'updated_by' => Auth::id(),
                    ]);
                    $parking = $existing;
                } else {
                    throw new \Exception(
                        "Vehicle plate {$vehiclePlate} is already registered for this property."
                    );
                }
            } elseif ($existing && $existing->trashed()) {
                $existing->restore();
                $existing->update([
                    'parking_type' => $request->parking_type,
                    'owner_name' => $bookingTransaction->user_name,
                    'owner_phone' => $bookingTransaction->user_phone_number,
                    'user_id' => $bookingTransaction->user_id,
                    'order_id' => $request->order_id,
                    'parking_duration' => $request->parking_duration,
                    'fee_amount' => $request->fee_amount,
                    'notes' => $request->notes,
                    'status' => 1,
                    'management_only' => 1,
                    'updated_by' => Auth::id(),
                ]);
                $parking = $existing;
            } else {
                $parking = Parking::create([
                    'property_id' => $bookingTransaction->property_id,
                    'parking_type' => $request->parking_type,
                    'vehicle_plate' => $vehiclePlate,
                    'owner_name' => $bookingTransaction->user_name,
                    'owner_phone' => $bookingTransaction->user_phone_number,
                    'user_id' => $bookingTransaction->user_id,
                    'order_id' => $request->order_id,
                    'parking_duration' => $request->parking_duration,
                    'fee_amount' => $request->fee_amount,
                    'notes' => $request->notes,
                    'status' => 1,
                    'management_only' => 1,
                    'created_by' => Auth::id(),
                ]);
            }

            // Increment quota untuk parkir baru (bukan renewal)
            // Parking Management juga mengkonsumsi quota karena kendaraan fisik sudah menempati slot
            if (!$isRenewal && $parkingFee->capacity > 0) {
                $parkingFee->incrementQuota();
            }

            DB::commit();

            $message = $isRenewal
                ? 'Parking registration added successfully (quota unchanged - renewal)'
                : 'Parking registration added successfully';

            if (!$isRenewal && $parkingFee->capacity > 0) {
                $remaining = $parkingFee->fresh()->available_quota;
                $message .= ". Parking quota: {$remaining}/{$parkingFee->capacity} available.";
            } elseif ($parkingFee->capacity === 0) {
                $message .= ' (unlimited quota).';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $parking,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add parking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $idrec)
    {
        $validated = $request->validate([
            'parking_type' => 'required|in:car,motorcycle',
            'vehicle_plate' => 'required|string|max:20',
            'owner_name' => 'nullable|string|max:150',
            'owner_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $parking = Parking::findOrFail($idrec);
            $oldParkingType = $parking->parking_type;
            $typeChanged = $validated['parking_type'] !== $oldParkingType;

            // Check unique if plate changed
            $newPlate = strtoupper($validated['vehicle_plate']);
            if ($newPlate !== $parking->vehicle_plate || $typeChanged) {
                $existing = Parking::where('property_id', $parking->property_id)
                    ->where('vehicle_plate', $newPlate)
                    ->where('idrec', '!=', $idrec)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => __('ui.parking_plate_exists')
                    ], 422);
                }

                // Check fee exists for new type
                $newParkingFee = ParkingFee::where('property_id', $parking->property_id)
                    ->where('parking_type', $validated['parking_type'])
                    ->where('status', 1)
                    ->first();

                if (!$newParkingFee) {
                    return response()->json([
                        'success' => false,
                        'message' => __('ui.parking_fee_not_configured')
                    ], 422);
                }

                // Adjust quota if parking type changed
                if ($typeChanged) {
                    // Decrement old type quota (decrementQuota handles quota_used=0 safely)
                    $oldParkingFee = ParkingFee::where('property_id', $parking->property_id)
                        ->where('parking_type', $oldParkingType)
                        ->where('status', 1)
                        ->first();

                    if ($oldParkingFee && $oldParkingFee->capacity > 0) {
                        $oldParkingFee->decrementQuota();
                    }

                    // Check new type quota availability
                    if ($newParkingFee->capacity > 0 && !$newParkingFee->hasAvailableQuota()) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Kuota parkir ' . ucfirst($validated['parking_type']) . ' sudah penuh. Tersedia: 0, Kapasitas: ' . $newParkingFee->capacity
                        ], 422);
                    }

                    // Increment new type quota
                    if ($newParkingFee->capacity > 0) {
                        $newParkingFee->incrementQuota();
                    }

                    // Update parking_type on active paid transactions if linked to PP
                    if (!$parking->management_only) {
                        ParkingFeeTransaction::where('parking_id', $parking->idrec)
                            ->where('transaction_status', 'paid')
                            ->update([
                                'parking_type' => $validated['parking_type'],
                                'fee_amount' => $newParkingFee->fee,
                                'updated_by' => Auth::id(),
                            ]);
                    }
                }
            }

            $parking->update([
                'parking_type' => $validated['parking_type'],
                'vehicle_plate' => $newPlate,
                'owner_name' => $validated['owner_name'] ?? null,
                'owner_phone' => $validated['owner_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('ui.parking_updated_success'),
                'data' => $parking,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($idrec)
    {
        try {
            DB::beginTransaction();

            $parking = Parking::findOrFail($idrec);

            // Jika quota dikonsumsi oleh Parking Management (management_only=1),
            // maka kurangi quota saat record dihapus (slot parkir dikosongkan)
            if ($parking->management_only) {
                $parkingFee = ParkingFee::where('property_id', $parking->property_id)
                    ->where('parking_type', $parking->parking_type)
                    ->where('status', 1)
                    ->first();

                if ($parkingFee && $parkingFee->capacity > 0) {
                    $parkingFee->decrementQuota();
                }
            }

            $parking->update(['updated_by' => Auth::id()]);
            $parking->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('ui.parking_deleted_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore($idrec)
    {
        try {
            DB::beginTransaction();

            $parking = Parking::withTrashed()->findOrFail($idrec);
            $parking->restore();
            $parking->update(['updated_by' => Auth::id()]);

            // Jika quota sebelumnya dikonsumsi oleh Parking Management,
            // maka tambah kembali quota saat record di-restore
            if ($parking->management_only) {
                $parkingFee = ParkingFee::where('property_id', $parking->property_id)
                    ->where('parking_type', $parking->parking_type)
                    ->where('status', 1)
                    ->first();

                if ($parkingFee && $parkingFee->capacity > 0) {
                    $parkingFee->incrementQuota();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('ui.parking_restored_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan data parkir: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        try {
            $parking = Parking::find($request->id);

            if (!$parking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data parkir tidak ditemukan'
                ], 404);
            }

            $parking->update([
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }
}
