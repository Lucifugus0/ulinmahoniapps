<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

/**
 * Calculates refund amounts for booking cancellations.
 *
 * Refund tiers based on days before check-in:
 * - >10 days: 75% of room price + parking/others
 * - 9-7 days: 50%
 * - 6-3 days: 25%
 * - <3 days: 0%
 * Deposit is always refunded 100%.
 * Service fees and admin fees are NOT refunded.
 */
class RefundCalculationService
{
    /**
     * Calculate refund breakdown for a transaction.
     *
     * @param Transaction $transaction
     * @return array{
     *   room_refund: float,
     *   deposit_refund: float,
     *   other_refund: float,
     *   total_refund: float,
     *   days_before_checkin: int,
     *   refund_percentage: int,
     *   requires_bank_account: bool
     * }
     */
    public function calculate(Transaction $transaction): array
    {
        $checkInDate = Carbon::parse($transaction->check_in)->startOfDay();
        $now = Carbon::now()->startOfDay();
        $daysBeforeCheckin = $now->diffInDays($checkInDate, false);

        // Determine refund percentage based on days before check-in
        $percentage = $this->getRefundPercentage($daysBeforeCheckin);

        // Room price refund (tiered percentage)
        $roomRefund = round(($transaction->room_price ?? 0) * $percentage / 100, 4);

        // Deposit is always 100% refunded
        $depositRefund = round($transaction->deposit_fee ?? 0, 4);

        // Parking and other fees use the same tiered percentage as room
        $otherRefund = round(($transaction->parking_fee ?? 0) * $percentage / 100, 4);

        // Service fees and admin fees are NOT refunded
        $totalRefund = $roomRefund + $depositRefund + $otherRefund;

        // QRIS and VA payments require bank account for refund
        $requiresBankAccount = $this->requiresBankAccount($transaction);

        return [
            'room_refund' => $roomRefund,
            'deposit_refund' => $depositRefund,
            'other_refund' => $otherRefund,
            'total_refund' => $totalRefund,
            'days_before_checkin' => max(0, $daysBeforeCheckin),
            'refund_percentage' => $percentage,
            'requires_bank_account' => $requiresBankAccount,
        ];
    }

    /**
     * Get the refund percentage based on days before check-in.
     */
    public function getRefundPercentage(int $daysBeforeCheckin): int
    {
        if ($daysBeforeCheckin > 10) {
            return 75;
        } elseif ($daysBeforeCheckin >= 7) {
            return 50;
        } elseif ($daysBeforeCheckin >= 3) {
            return 25;
        } else {
            return 0;
        }
    }

    /**
     * Check if the payment method requires a bank account for refund (QRIS, VA).
     */
    public function requiresBankAccount(Transaction $transaction): bool
    {
        $type = strtolower($transaction->transaction_type ?? '');

        // QRIS payments
        if ($type === 'qris') {
            return true;
        }

        // Virtual Account payments (bank names like mandiri, bni, bca, cimb, etc.)
        // Credit Card and BRI Manual do not require bank account
        $nonVaTypes = ['credit card', 'bri manual', 'qris', ''];
        if (!in_array($type, $nonVaTypes) && !empty($transaction->virtual_account_no)) {
            return true;
        }

        return false;
    }
}
