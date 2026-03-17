<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    /**
     * Generate unique voucher code
     *
     * @param int $length Length of code (default: 8)
     * @param string|null $prefix Optional prefix
     * @return string
     */
    public function generateCode($length = 8, $prefix = null)
    {
        $length = max(8, min(20, $length)); // Enforce 8-20 character limit

        do {
            // Generate random alphanumeric code
            $code = strtoupper(Str::random($length));

            // Add prefix if provided
            if ($prefix) {
                $prefixLength = min(strlen($prefix), 5);
                $remainingLength = $length - $prefixLength;
                $code = strtoupper(substr($prefix, 0, $prefixLength)) . strtoupper(Str::random($remainingLength));
            }

            // Check uniqueness
            $exists = Voucher::where('code', $code)->exists();
        } while ($exists);

        return $code;
    }

    /**
     * Validate voucher for use
     *
     * @param string $code Voucher code
     * @param int $userId User ID attempting to use voucher
     * @param float $transactionAmount Transaction amount
     * @param int|null $propertyId Property ID (optional)
     * @param int|null $roomId Room ID (optional)
     * @return array ['valid' => bool, 'voucher' => Voucher|null, 'errors' => array]
     */
    public function validateVoucher($code, $userId, $transactionAmount, $propertyId = null, $roomId = null)
    {
        $errors = [];

        // Find voucher by code
        $voucher = Voucher::byCode($code)->first();

        if (!$voucher) {
            return [
                'valid' => false,
                'voucher' => null,
                'errors' => ['Voucher code not found']
            ];
        }

        // Check status
        if ($voucher->status !== 'active') {
            $errors[] = 'This voucher is no longer active';
        }

        // Check date validity
        $now = Carbon::now();
        if ($now->isBefore($voucher->valid_from)) {
            $errors[] = 'This voucher is not yet valid. Valid from: ' . $voucher->valid_from->format('d M Y H:i');
        }

        if ($now->isAfter($voucher->valid_to)) {
            $errors[] = 'This voucher has expired on: ' . $voucher->valid_to->format('d M Y H:i');
        }

        // Check minimum transaction amount
        if ($transactionAmount < $voucher->min_transaction_amount) {
            $errors[] = 'Minimum transaction amount is Rp ' . number_format($voucher->min_transaction_amount, 0, ',', '.');
        }

        // Check total usage limit
        if ($voucher->max_total_usage > 0 && $voucher->current_usage_count >= $voucher->max_total_usage) {
            $errors[] = 'This voucher has reached its maximum usage limit';
        }

        // Check user eligibility (usage per user limit)
        $userUsageCount = VoucherUsage::where('voucher_id', $voucher->idrec)
            ->where('user_id', $userId)
            ->where('status', 'applied')
            ->count();

        if ($userUsageCount >= $voucher->max_usage_per_user) {
            $errors[] = 'You have already used this voucher the maximum number of times';
        }

        // Check if voucher is bound to a specific property via property_id column
        // property_id = 0 or NULL means global (can be used at any property)
        $propertyValidated = false;
        if (!empty($voucher->property_id) && $voucher->property_id != 0) {
            // Voucher is bound to a specific property - property_id is REQUIRED in request
            if (!$propertyId) {
                $errors[] = 'This voucher requires a property_id to be specified';
            } elseif ($voucher->property_id != $propertyId) {
                $errors[] = 'This voucher is not valid for the selected property';
            }
            $propertyValidated = true;  // Property was validated via property_id column
        }

        // Check property/room scope restrictions (legacy scope_type/scope_ids)
        // Only check scope_ids if property was NOT already validated via property_id
        if (!$propertyValidated && $voucher->scope_type === 'property' && $propertyId) {
            $scopeIds = $voucher->scope_ids ?? [];
            if (!empty($scopeIds) && !in_array($propertyId, $scopeIds)) {
                $errors[] = 'This voucher is not valid for the selected property';
            }
        }

        if ($voucher->scope_type === 'room' && $roomId) {
            $scopeIds = $voucher->scope_ids ?? [];
            if (!in_array($roomId, $scopeIds)) {
                $errors[] = 'This voucher is not valid for the selected room';
            }
        }

        return [
            'valid' => empty($errors),
            'voucher' => $voucher,
            'errors' => $errors
        ];
    }

    /**
     * Check if user is eligible to use voucher
     *
     * @param int $voucherId Voucher ID
     * @param int $userId User ID
     * @return array ['eligible' => bool, 'usage_count' => int, 'max_allowed' => int]
     */
    public function checkUserEligibility($voucherId, $userId)
    {
        $voucher = Voucher::find($voucherId);

        if (!$voucher) {
            return [
                'eligible' => false,
                'usage_count' => 0,
                'max_allowed' => 0,
                'message' => 'Voucher not found'
            ];
        }

        $usageCount = VoucherUsage::where('voucher_id', $voucherId)
            ->where('user_id', $userId)
            ->where('status', 'applied')
            ->count();

        $eligible = $usageCount < $voucher->max_usage_per_user;

        return [
            'eligible' => $eligible,
            'usage_count' => $usageCount,
            'max_allowed' => $voucher->max_usage_per_user,
            'message' => $eligible
                ? "You can use this voucher {$voucher->max_usage_per_user} time(s). Current usage: {$usageCount}"
                : "You have reached the maximum usage limit for this voucher"
        ];
    }

    /**
     * Calculate discount amount
     *
     * @param Voucher $voucher Voucher instance
     * @param float $amount Original transaction amount
     * @return array ['discount_amount' => float, 'final_amount' => float]
     */
    public function calculateDiscount(Voucher $voucher, $amount)
    {
        // Calculate percentage discount
        $discountAmount = ($amount * $voucher->discount_percentage) / 100;

        // Apply maximum discount cap only if max_discount_amount is set (> 0)
        if ($voucher->max_discount_amount > 0 && $discountAmount > $voucher->max_discount_amount) {
            $discountAmount = $voucher->max_discount_amount;
        }

        // Calculate final amount
        $finalAmount = max(0, $amount - $discountAmount);

        return [
            'original_amount' => round($amount, 2),
            'discount_amount' => round($discountAmount, 2),
            'final_amount' => round($finalAmount, 2),
            'discount_percentage' => $voucher->discount_percentage,
            'max_discount_cap' => $voucher->max_discount_amount
        ];
    }

    /**
     * Apply voucher to a transaction
     *
     * @param string $code Voucher code
     * @param int $userId User ID
     * @param float $amount Transaction amount
     * @param array $bookingDetails Booking details (property_id, room_id, etc.)
     * @return array ['success' => bool, 'data' => array, 'errors' => array]
     */
    public function applyVoucher($code, $userId, $amount, $bookingDetails = [])
    {
        try {
            DB::beginTransaction();

            // Validate voucher
            $validation = $this->validateVoucher(
                $code,
                $userId,
                $amount,
                $bookingDetails['property_id'] ?? null,
                $bookingDetails['room_id'] ?? null
            );

            if (!$validation['valid']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'data' => null,
                    'errors' => $validation['errors']
                ];
            }

            $voucher = $validation['voucher'];

            // Calculate discount
            $calculation = $this->calculateDiscount($voucher, $amount);

            // Increment usage count
            $voucher->increment('current_usage_count');

            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'voucher_id' => $voucher->idrec,
                    'voucher_code' => $voucher->code,
                    'voucher_name' => $voucher->name,
                    'original_amount' => $calculation['original_amount'],
                    'discount_amount' => $calculation['discount_amount'],
                    'final_amount' => $calculation['final_amount'],
                    'discount_percentage' => $calculation['discount_percentage'],
                    'max_discount_cap' => $calculation['max_discount_cap']
                ],
                'errors' => []
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error applying voucher: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'errors' => ['An error occurred while applying the voucher: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Log voucher usage
     *
     * @param array $data Usage data
     * @return VoucherUsage|null
     */
    public function logUsage($data)
    {
        try {
            $usageData = [
                'voucher_id' => $data['voucher_id'],
                'voucher_code' => $data['voucher_code'],
                'user_id' => $data['user_id'],
                'order_id' => $data['order_id'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'property_id' => $data['property_id'] ?? null,
                'room_id' => $data['room_id'] ?? null,
                'original_amount' => $data['original_amount'],
                'discount_amount' => $data['discount_amount'],
                'final_amount' => $data['final_amount'],
                'used_at' => Carbon::now(),
                'status' => 'applied',
                'metadata' => $data['metadata'] ?? null
            ];

            return VoucherUsage::create($usageData);

        } catch (\Exception $e) {
            Log::error('Error logging voucher usage: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get voucher usage history for a user
     *
     * @param int $userId User ID
     * @param array $filters Optional filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserUsageHistory($userId, $filters = [])
    {
        $query = VoucherUsage::with(['voucher', 'transaction', 'property', 'room'])
            ->where('user_id', $userId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['voucher_id'])) {
            $query->where('voucher_id', $filters['voucher_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('used_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('used_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('used_at', 'desc')->get();
    }

    /**
     * Get voucher usage statistics
     *
     * @param int $voucherId Voucher ID
     * @return array|null
     */
    public function getVoucherStatistics($voucherId)
    {
        $voucher = Voucher::find($voucherId);

        if (!$voucher) {
            return null;
        }

        $usages = VoucherUsage::where('voucher_id', $voucherId)
            ->where('status', 'applied')
            ->get();

        return [
            'voucher_code' => $voucher->code,
            'voucher_name' => $voucher->name,
            'total_usage' => $usages->count(),
            'unique_users' => $usages->unique('user_id')->count(),
            'total_discount_given' => $usages->sum('discount_amount'),
            'average_discount' => $usages->avg('discount_amount'),
            'total_original_amount' => $usages->sum('original_amount'),
            'total_final_amount' => $usages->sum('final_amount'),
            'max_usage_limit' => $voucher->max_total_usage,
            'remaining_usage' => $voucher->remaining_usage,
            'is_active' => $voucher->status === 'active',
            'valid_from' => $voucher->valid_from,
            'valid_to' => $voucher->valid_to,
            'is_expired' => $voucher->is_expired
        ];
    }

    /**
     * Cancel/refund voucher usage
     *
     * @param int $usageId Usage ID
     * @param string|null $reason Cancellation reason
     * @return bool
     */
    public function cancelUsage($usageId, $reason = null)
    {
        try {
            DB::beginTransaction();

            $usage = VoucherUsage::find($usageId);

            if (!$usage || $usage->status !== 'applied') {
                DB::rollBack();
                return false;
            }

            // Update usage status
            $usage->status = 'cancelled';
            $metadata = $usage->metadata ?? [];
            $metadata['cancellation_reason'] = $reason;
            $metadata['cancelled_at'] = Carbon::now()->toDateTimeString();
            $usage->metadata = $metadata;
            $usage->save();

            // Decrement voucher usage count
            $voucher = $usage->voucher;
            if ($voucher) {
                $voucher->decrement('current_usage_count');
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling voucher usage: ' . $e->getMessage());
            return false;
        }
    }
}
