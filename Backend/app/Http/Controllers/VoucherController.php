<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Voucher::with(['creator'])
            ->orderBy('created_at', 'desc');

        // Apply filters if present
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $vouchers = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        $properties = Property::where('status', 1)->get();
        $rooms = Room::where('status', 1)->get();

        return view('pages.vouchers.index', compact('vouchers', 'perPage', 'properties', 'rooms'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi data
            $validated = $request->validate([
                'code' => 'required|string|min:8|max:12|unique:m_vouchers,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'discount_percentage' => 'required|numeric|min:0|max:100',
                'max_discount_amount' => 'required|numeric|min:0',
                'max_total_usage' => 'required|integer|min:0',
                'max_usage_per_user' => 'required|integer|min:1',
                'valid_from' => 'required|date',
                'valid_to' => 'required|date|after:valid_from',
                'min_transaction_amount' => 'nullable|numeric|min:0',
                'scope_type' => 'required|in:global,property',
                'property_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->scope_type === 'property' && empty($value)) {
                            $fail('The property id field is required when scope type is property.');
                        }
                        if ($request->scope_type === 'property' && !empty($value)) {
                            // Check if property exists
                            if (!\App\Models\Property::where('idrec', $value)->exists()) {
                                $fail('The selected property id is invalid.');
                            }
                        }
                    },
                ],
                'status' => 'required|in:active,inactive',
            ]);

            // Uppercase voucher code
            $validated['code'] = strtoupper($validated['code']);

            // Handle property_id based on scope_type
            if ($validated['scope_type'] === 'property') {
                $validated['property_id'] = $request->property_id;
            } else {
                $validated['property_id'] = null;
            }

            // Konversi nilai numeric
            $validated['discount_percentage'] = (float) $validated['discount_percentage'];
            $validated['max_discount_amount'] = (float) $validated['max_discount_amount'];
            $validated['min_transaction_amount'] = $validated['min_transaction_amount'] ? (float) $validated['min_transaction_amount'] : 0;
            $validated['max_total_usage'] = (int) $validated['max_total_usage'];
            $validated['max_usage_per_user'] = (int) $validated['max_usage_per_user'];

            // Set default values
            $validated['created_by'] = Auth::id();
            $validated['current_usage_count'] = 0;

            // Buat voucher
            $voucher = Voucher::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil ditambahkan',
                'data' => $voucher
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Gagal menambahkan voucher: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $voucher = Voucher::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'discount_percentage' => 'required|numeric|min:0|max:100',
                'max_discount_amount' => 'required|numeric|min:0',
                'max_total_usage' => 'required|integer|min:0',
                'max_usage_per_user' => 'required|integer|min:1',
                'valid_from' => 'required|date',
                'valid_to' => 'required|date|after:valid_from',
                'min_transaction_amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:active,inactive,expired',
            ]);

            $validated['updated_by'] = Auth::id();

            // code & scope_type are locked — keep existing values from DB
            $validated['code'] = $voucher->code;
            $validated['scope_type'] = $voucher->scope_type;
            $validated['property_id'] = $voucher->property_id;

            $voucher->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diupdate',
                'data' => $voucher
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $voucher = Voucher::with(['creator', 'updater'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Voucher::with(['creator'])
            ->orderBy('created_at', 'desc');

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }

        // Status Filter
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        // Pagination
        $vouchers = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.vouchers.partials.voucher_table', [
                'vouchers' => $vouchers,
                'per_page' => $perPage,
            ])->render(),
            'pagination' => $perPage !== 'all'
                ? $vouchers->links()->toHtml()
                : ''
        ]);
    }

    public function toggleStatus(Request $request)
    {
        try {
            $voucher = Voucher::findOrFail($request->id);
            $voucher->status = $request->status;
            $voucher->updated_by = Auth::id();
            $voucher->save();

            return response()->json([
                'success' => true,
                'message' => 'Status voucher berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status voucher'
            ], 500);
        }
    }
}
