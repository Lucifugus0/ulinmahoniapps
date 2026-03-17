<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\VoucherService;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends ApiController
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }

    /**
     * Validate voucher code (check before applying)
     * POST /api/v1/voucher/validate
     */
    public function validateVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_code' => 'required|string|min:8|max:20',
            'user_id' => 'required|integer|exists:users,id',
            'transaction_amount' => 'nullable|numeric|min:0',
            'property_id' => 'nullable|integer|exists:m_properties,idrec',
            'room_id' => 'nullable|integer|exists:m_rooms,idrec'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $result = $this->voucherService->validateVoucher(
            $request->voucher_code,
            $request->user_id,
            $request->transaction_amount,
            $request->property_id,
            $request->room_id
        );

        if (!$result['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher validation failed',
                'errors' => $result['errors']
            ], 422);
        }

        $voucher = $result['voucher'];
        $calculation = $this->voucherService->calculateDiscount($voucher, $request->transaction_amount);

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher is valid',
            'data' => [
                'voucher' => [
                    'voucher_code' => $voucher->voucher,
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'discount_percentage' => $voucher->discount_percentage,
                    'max_discount_amount' => $voucher->max_discount_amount
                ],
                'calculation' => $calculation,
                'eligibility' => $this->voucherService->checkUserEligibility($voucher->idrec, $request->user_id)
            ]
        ]);
    }

    /**
     * Apply voucher (used during booking)
     * POST /api/v1/voucher/apply
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_code' => 'required|string|min:8|max:20',
            'user_id' => 'required|integer|exists:users,id',
            'transaction_amount' => 'required|numeric|min:0',
            'property_id' => 'nullable|integer|exists:m_properties,idrec',
            'room_id' => 'nullable|integer|exists:m_rooms,idrec'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $bookingDetails = [
            'property_id' => $request->property_id,
            'room_id' => $request->room_id
        ];

        $result = $this->voucherService->applyVoucher(
            $request->voucher_code,
            $request->user_id,
            $request->transaction_amount,
            $bookingDetails
        );

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to apply voucher',
                'errors' => $result['errors']
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher applied successfully',
            'data' => $result['data']
        ]);
    }

    /**
     * Get user's voucher usage history
     * GET /api/v1/voucher/history/{user_id}
     */
    public function getUserHistory($user_id, Request $request)
    {
        $validator = Validator::make(array_merge(['user_id' => $user_id], $request->all()), [
            'user_id' => 'required|integer|exists:users,id',
            'status' => 'nullable|in:applied,cancelled,refunded',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $filters = $request->only(['status', 'date_from', 'date_to']);
        $history = $this->voucherService->getUserUsageHistory($user_id, $filters);

        return response()->json([
            'status' => 'success',
            'message' => 'Usage history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * List available vouchers (for user to browse)
     * GET /api/v1/voucher/available
     */
    public function availableVouchers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
            'property_id' => 'nullable|integer|exists:m_properties,idrec',
            'room_id' => 'nullable|integer|exists:m_rooms,idrec'
        ]);

        if ($validator->fails()) {
            return $this->respondBadRequest('Validation failed', $validator->errors());
        }

        $query = Voucher::available();

        // Filter by property_id (new column - voucher bound to specific property)
        if ($request->has('property_id')) {
            $query->forProperty($request->property_id);
        }

        // Filter by scope (legacy scope_type/scope_ids)
        if ($request->has('property_id')) {
            $query->where(function($q) use ($request) {
                $q->where('scope_type', 'global')
                  ->orWhereNull('scope_type')
                  ->orWhere(function($sq) use ($request) {
                      $sq->where('scope_type', 'property')
                         ->whereJsonContains('scope_ids', (int) $request->property_id);
                  });
            });
        }

        if ($request->has('room_id')) {
            $query->where(function($q) use ($request) {
                $q->where('scope_type', 'global')
                  ->orWhereNull('scope_type')
                  ->orWhere(function($sq) use ($request) {
                      $sq->where('scope_type', 'room')
                         ->whereJsonContains('scope_ids', (int) $request->room_id);
                  });
            });
        }

        $vouchers = $query->with('property')->orderBy('discount_percentage', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Available vouchers retrieved successfully',
            'data' => $vouchers
        ]);
    }
}
