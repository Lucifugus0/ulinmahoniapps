<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositFee;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DepositFeeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = DepositFee::with(['property', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc');

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

        $depositFees = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        // Get properties for the add form
        $propertiesQuery = Property::where('status', 1)->orderBy('name');
        if ($accessiblePropertyId !== null) {
            $propertiesQuery->where('idrec', $accessiblePropertyId);
        }
        $properties = $propertiesQuery->get();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.Properties.Deposit_fees.partials.deposit-fee_table', compact('depositFees'));
        }

        return view('pages.Properties.Deposit_fees.index', compact('depositFees', 'properties'));
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');
        $status = $request->input('status', '');

        $query = DepositFee::with(['property', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc');

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

        $depositFees = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.Properties.Deposit_fees.partials.deposit-fee_table', compact('depositFees'))->render(),
            'pagination' => $perPage !== 'all' && $depositFees instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $depositFees->links()->toHtml()
                : ''
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:m_properties,idrec',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $existing = DepositFee::where('property_id', $validated['property_id'])->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deposit fee untuk property ini sudah ada. Silakan edit yang sudah ada.'
                ], 422);
            }

            $depositFee = DepositFee::create([
                'property_id' => $validated['property_id'],
                'amount' => $validated['amount'],
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit fee berhasil ditambahkan',
                'data' => $depositFee,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan deposit fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $idrec)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $depositFee = DepositFee::findOrFail($idrec);

            $depositFee->update([
                'amount' => $validated['amount'],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit fee berhasil diperbarui',
                'data' => $depositFee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui deposit fee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        try {
            $depositFee = DepositFee::find($request->id);

            if (!$depositFee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deposit fee not found'
                ], 404);
            }

            $depositFee->update([
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
