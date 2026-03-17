<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParkingFee;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class ParkingFeeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $showDeleted = $request->input('show_deleted', '0');

        $query = ParkingFee::with(['property', 'createdBy', 'updatedBy'])
            ->orderBy('property_id', 'asc');

        if ($showDeleted === '1') {
            $query->withTrashed();
        }

        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            $query->where('property_id', $accessiblePropertyId);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status !== '' && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('parking_type') && !empty($request->parking_type)) {
            $query->where('parking_type', $request->parking_type);
        }

        $parkingFees = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        $propertiesQuery = Property::where('status', 1)->orderBy('name');
        if ($accessiblePropertyId !== null) {
            $propertiesQuery->where('idrec', $accessiblePropertyId);
        }
        $properties = $propertiesQuery->get();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.Properties.Parking_fees.partials.parking-fee_table', compact('parkingFees'));
        }

        return view('pages.Properties.Parking_fees.index', compact('parkingFees', 'properties'));
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');
        $status = $request->input('status', '');
        $parkingType = $request->input('parking_type', '');
        $showDeleted = $request->input('show_deleted', '0');

        $query = ParkingFee::with(['property', 'createdBy', 'updatedBy'])
            ->orderBy('property_id', 'asc');

        if ($showDeleted === '1') {
            $query->withTrashed();
        }

        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();

        if ($accessiblePropertyId !== null) {
            $query->where('property_id', $accessiblePropertyId);
        }

        if (!empty($search)) {
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($parkingType)) {
            $query->where('parking_type', $parkingType);
        }

        $parkingFees = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.Properties.Parking_fees.partials.parking-fee_table', compact('parkingFees'))->render(),
            'pagination' => $perPage !== 'all' && $parkingFees instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $parkingFees->links()->toHtml()
                : ''
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:m_properties,idrec',
            'parking_type' => 'required|in:car,motorcycle',
            'fee' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:0',
        ]);

        try {
            $existing = ParkingFee::withTrashed()
                ->where('property_id', $validated['property_id'])
                ->where('parking_type', $validated['parking_type'])
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parking fee untuk tipe ini pernah ada dan sudah dihapus. Silakan pulihkan (restore) data yang sudah ada.'
                    ], 422);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Parking fee untuk tipe ini di property ini sudah ada. Silakan edit yang sudah ada.'
                ], 422);
            }

            $parkingFee = ParkingFee::create([
                'property_id' => $validated['property_id'],
                'parking_type' => $validated['parking_type'],
                'fee' => $validated['fee'],
                'capacity' => $validated['capacity'],
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parking fee berhasil ditambahkan',
                'data' => $parkingFee,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan parking fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $idrec)
    {
        $validated = $request->validate([
            'fee' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:0',
        ]);

        try {
            $parkingFee = ParkingFee::findOrFail($idrec);

            $parkingFee->update([
                'fee' => $validated['fee'],
                'capacity' => $validated['capacity'],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parking fee berhasil diperbarui',
                'data' => $parkingFee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui parking fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($idrec)
    {
        try {
            $parkingFee = ParkingFee::findOrFail($idrec);

            $parkingFee->update(['updated_by' => Auth::id()]);
            $parkingFee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Parking fee berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus parking fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore($idrec)
    {
        try {
            $parkingFee = ParkingFee::withTrashed()->findOrFail($idrec);
            $parkingFee->restore();
            $parkingFee->update(['updated_by' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Parking fee berhasil dipulihkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan parking fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adjustQuota(Request $request, $idrec)
    {
        $validated = $request->validate([
            'action' => 'required|in:increment,decrement',
            'amount' => 'integer|min:1',
        ]);

        try {
            $parkingFee = ParkingFee::findOrFail($idrec);
            $amount = $validated['amount'] ?? 1;

            if ($validated['action'] === 'increment') {
                if ($parkingFee->capacity > 0 && $parkingFee->quota_used >= $parkingFee->capacity) {
                    return response()->json(['success' => false, 'message' => 'Kapasitas parkir sudah penuh'], 422);
                }
                $parkingFee->increment('quota_used', $amount);
            } else {
                if ($parkingFee->quota_used < $amount) {
                    return response()->json(['success' => false, 'message' => 'Quota sudah mencapai 0'], 422);
                }
                $parkingFee->decrement('quota_used', $amount);
            }

            $parkingFee->update(['updated_by' => Auth::id()]);
            $parkingFee->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Quota berhasil diperbarui',
                'quota_used' => $parkingFee->quota_used,
                'available_quota' => $parkingFee->available_quota,
                'quota_usage_percentage' => $parkingFee->quota_usage_percentage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah quota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        try {
            $parkingFee = ParkingFee::find($request->id);

            if (!$parkingFee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parking fee not found'
                ], 404);
            }

            $parkingFee->update([
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
