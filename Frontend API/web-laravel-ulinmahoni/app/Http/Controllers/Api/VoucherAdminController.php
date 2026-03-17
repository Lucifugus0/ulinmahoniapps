<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\VoucherService;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VoucherAdminController extends ApiController
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }

    /**
     * List all vouchers with filters
     * GET /api/v1/admin/voucher
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:active,inactive,expired',
            'scope_type' => 'nullable|in:global,property,room',
            'property_id' => 'nullable|integer',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $query = Voucher::with('property');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('scope_type')) {
            $query->where('scope_type', $request->scope_type);
        }

        // Filter by property_id (works with scope_type = property)
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->has('date_from')) {
            $query->where('valid_from', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('valid_to', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        // Pagination
        if ($request->has('limit') && $request->has('page')) {
            $vouchers = $query->paginate($request->limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Vouchers retrieved successfully',
                'data' => $vouchers->items(),
                'meta' => [
                    'current_page' => $vouchers->currentPage(),
                    'last_page' => $vouchers->lastPage(),
                    'per_page' => $vouchers->perPage(),
                    'total' => $vouchers->total()
                ]
            ]);
        }

        $vouchers = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Vouchers retrieved successfully',
            'data' => $vouchers
        ]);
    }

    /**
     * Show specific voucher
     * GET /api/v1/admin/voucher/{id}
     */
    public function show($id)
    {
        $voucher = Voucher::with(['usages', 'creator', 'updater'])->find($id);

        if (!$voucher) {
            return $this->respondNotFound('Voucher not found');
        }

        $statistics = $this->voucherService->getVoucherStatistics($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher retrieved successfully',
            'data' => [
                'voucher' => $voucher,
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Create new voucher
     * POST /api/v1/admin/voucher
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|min:8|max:20|unique:m_vouchers,code',
            'auto_generate_code' => 'nullable|boolean',
            'code_prefix' => 'nullable|string|max:5',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'max_discount_amount' => 'required|numeric|min:0',
            'max_total_usage' => 'nullable|integer|min:0',
            'max_usage_per_user' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'scope_type' => 'required|in:global,property,room',
            'scope_ids' => 'nullable|array',
            'scope_ids.*' => 'integer',
            'property_id' => 'nullable|integer|exists:m_properties,idrec',
            'status' => 'nullable|in:active,inactive',
            'created_by' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        try {
            DB::beginTransaction();

            // Generate code if needed
            $code = $request->code;
            if ($request->auto_generate_code || !$code) {
                $code = $this->voucherService->generateCode(10, $request->code_prefix);
            }

            $voucherData = [
                'code' => strtoupper($code),
                'name' => $request->name,
                'description' => $request->description,
                'discount_percentage' => $request->discount_percentage,
                'max_discount_amount' => $request->max_discount_amount,
                'max_total_usage' => $request->max_total_usage ?? 0,
                'current_usage_count' => 0,
                'max_usage_per_user' => $request->max_usage_per_user,
                'valid_from' => $request->valid_from,
                'valid_to' => $request->valid_to,
                'min_transaction_amount' => $request->min_transaction_amount ?? 0,
                'scope_type' => $request->scope_type,
                'scope_ids' => $request->scope_ids,
                'property_id' => $request->property_id ?? null,  // NULL or 0 = global, otherwise bound to property
                'status' => $request->status ?? 'active',
                'created_by' => $request->created_by,
                'updated_by' => $request->created_by
            ];

            $voucher = Voucher::create($voucherData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher created successfully',
                'data' => $voucher
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondInternalError('Error creating voucher: ' . $e->getMessage());
        }
    }

    /**
     * Update voucher
     * PUT /api/v1/admin/voucher/{id}
     */
    public function update($id, Request $request)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return $this->respondNotFound('Voucher not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_total_usage' => 'nullable|integer|min:0',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after:valid_from',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'scope_type' => 'nullable|in:global,property,room',
            'scope_ids' => 'nullable|array',
            'scope_ids.*' => 'integer',
            'property_id' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive,expired',
            'updated_by' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        try {
            DB::beginTransaction();

            // Update only provided fields
            $updateData = $request->only([
                'name',
                'description',
                'discount_percentage',
                'max_discount_amount',
                'max_total_usage',
                'max_usage_per_user',
                'valid_from',
                'valid_to',
                'min_transaction_amount',
                'scope_type',
                'scope_ids',
                'property_id',
                'status'
            ]);

            $updateData['updated_by'] = $request->updated_by;

            $voucher->update($updateData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher updated successfully',
                'data' => $voucher->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondInternalError('Error updating voucher: ' . $e->getMessage());
        }
    }

    /**
     * Delete/deactivate voucher
     * DELETE /api/v1/admin/voucher/{id}
     */
    public function destroy($id, Request $request)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return $this->respondNotFound('Voucher not found');
        }

        $validator = Validator::make($request->all(), [
            'soft_delete' => 'nullable|boolean',
            'updated_by' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        try {
            DB::beginTransaction();

            $voucher->updated_by = $request->updated_by;
            $voucher->save();

            if ($request->soft_delete === false) {
                // Hard delete
                $voucher->forceDelete();
                $message = 'Voucher permanently deleted';
            } else {
                // Soft delete (default)
                $voucher->delete();
                $message = 'Voucher deactivated successfully';
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondInternalError('Error deleting voucher: ' . $e->getMessage());
        }
    }

    /**
     * Get voucher usage logs
     * GET /api/v1/admin/voucher/{id}/usage-logs
     */
    public function usageLogs($id, Request $request)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return $this->respondNotFound('Voucher not found');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:applied,cancelled,refunded',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $query = $voucher->usages()->with(['user', 'transaction', 'property', 'room']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('used_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('used_at', '<=', $request->date_to);
        }

        $query->orderBy('used_at', 'desc');

        // Pagination
        if ($request->has('limit') && $request->has('page')) {
            $logs = $query->paginate($request->limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Usage logs retrieved successfully',
                'data' => $logs->items(),
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total()
                ]
            ]);
        }

        $logs = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Usage logs retrieved successfully',
            'data' => $logs
        ]);
    }

    /**
     * Generate voucher code (utility endpoint)
     * POST /api/v1/admin/voucher/generate-code
     */
    public function generateCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'length' => 'nullable|integer|min:8|max:20',
            'prefix' => 'nullable|string|max:5',
            'count' => 'nullable|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $length = $request->length ?? 10;
        $prefix = $request->prefix;
        $count = $request->count ?? 1;

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->voucherService->generateCode($length, $prefix);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher codes generated successfully',
            'data' => [
                'codes' => $codes
            ]
        ]);
    }
}
