<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\DepositFee;
use App\Models\ParkingFee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Unified controller for managing deposit fees and parking fees per property.
 * Displays one row per property with deposit + motorcycle + car columns.
 * Delegates actual CRUD to existing DepositFee/ParkingFee models (no API changes).
 */
class PropertyFeesController extends Controller
{
    /**
     * Show the unified deposit & parking fee management page.
     * Loads properties with their deposit fee and parking fees eagerly.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        /* Build query: active properties with deposit + parking fees */
        $query = Property::with(['depositFee', 'parkingFees'])
            ->where('status', 1)
            ->orderBy('name', 'asc');

        /* Respect per-property access restriction */
        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();
        if ($accessiblePropertyId !== null) {
            $query->where('idrec', $accessiblePropertyId);
        }

        /* Search by property name */
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $properties = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        /* Property list for the add/edit modal selectors */
        $propertyListQuery = Property::where('status', 1)->orderBy('name');
        if ($accessiblePropertyId !== null) {
            $propertyListQuery->where('idrec', $accessiblePropertyId);
        }
        $propertyList = $propertyListQuery->get();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.Properties.Property_fees.partials.property-fees_table', compact('properties'));
        }

        return view('pages.Properties.Property_fees.index', compact('properties', 'propertyList'));
    }

    /**
     * AJAX filter endpoint — returns rendered table HTML + pagination.
     * Supports search (property name) and per_page.
     */
    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $search = $request->input('search');

        $query = Property::with(['depositFee', 'parkingFees'])
            ->where('status', 1)
            ->orderBy('name', 'asc');

        $user = Auth::user();
        $accessiblePropertyId = $user->getAccessiblePropertyId();
        if ($accessiblePropertyId !== null) {
            $query->where('idrec', $accessiblePropertyId);
        }

        if (!empty($search)) {
            $query->where('name', 'like', "%$search%");
        }

        $properties = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.Properties.Property_fees.partials.property-fees_table', compact('properties'))->render(),
            'pagination' => $perPage !== 'all' && $properties instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $properties->links()->toHtml()
                : ''
        ]);
    }

    /**
     * Store or update deposit fee and parking fees for a property.
     * Uses existing DepositFee::updateOrCreate and ParkingFee::updateOrCreate
     * within a DB transaction. Each section is optional.
     */
    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:m_properties,idrec',
            'deposit_amount' => 'nullable|numeric|min:0',
            'motorcycle_fee' => 'nullable|numeric|min:0',
            'motorcycle_capacity' => 'nullable|integer|min:0',
            'car_fee' => 'nullable|numeric|min:0',
            'car_capacity' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $propertyId = $validated['property_id'];
            $userId = Auth::id();

            /* Update or create deposit fee if amount is provided */
            if (isset($validated['deposit_amount']) && $validated['deposit_amount'] !== null && $validated['deposit_amount'] !== '') {
                DepositFee::updateOrCreate(
                    ['property_id' => $propertyId],
                    [
                        'amount' => $validated['deposit_amount'],
                        'status' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }

            /* Update or create motorcycle parking fee if fee is provided */
            if (isset($validated['motorcycle_fee']) && $validated['motorcycle_fee'] !== null && $validated['motorcycle_fee'] !== '') {
                ParkingFee::updateOrCreate(
                    ['property_id' => $propertyId, 'parking_type' => 'motorcycle'],
                    [
                        'fee' => $validated['motorcycle_fee'],
                        'capacity' => $validated['motorcycle_capacity'] ?? 0,
                        'status' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }

            /* Update or create car parking fee if fee is provided */
            if (isset($validated['car_fee']) && $validated['car_fee'] !== null && $validated['car_fee'] !== '') {
                ParkingFee::updateOrCreate(
                    ['property_id' => $propertyId, 'parking_type' => 'car'],
                    [
                        'fee' => $validated['car_fee'],
                        'capacity' => $validated['car_capacity'] ?? 0,
                        'status' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('ui.save') . ' - OK',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
