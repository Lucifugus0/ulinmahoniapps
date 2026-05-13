<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The order ID of the booking to expire
     *
     * @var string
     */
    public $orderId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param string $orderId
     * @return void
     */
    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            // Find the transaction
            $transaction = Transaction::where('order_id', $this->orderId)->first();

            if (!$transaction) {
                Log::warning("Transaction not found for order_id: {$this->orderId}");
                return;
            }

            // Only expire if still pending
            if ($transaction->transaction_status !== 'pending') {
                Log::info("Transaction {$this->orderId} is no longer pending. Current status: {$transaction->transaction_status}");
                return;
            }

            // Check if payment has been made
            $payment = Payment::where('order_id', $this->orderId)->first();
            if ($payment && $payment->payment_status === 'paid') {
                Log::info("Payment already completed for order_id: {$this->orderId}");
                return;
            }

            // Update transaction status to expired
            $transaction->update([
                'transaction_status' => 'expired',
                'status' => '0', // Inactive
            ]);

            // Update payment status if exists
            if ($payment) {
                $payment->update([
                    'payment_status' => 'expired'
                ]);
            }

            // Update booking status if exists
            $booking = Booking::where('order_id', $this->orderId)->first();
            if ($booking) {
                $booking->update([
                    'status' => '0' // Inactive
                ]);
            }

            /**
             * Soft-delete the bundled-flow `t_parking` row tied to this order.
             *
             * Bundled booking + parking inserts a `t_parking` row at booking creation
             * time (`BookingController::updatePaymentMethod`). Before this guard the
             * row stayed in the DB as if the slot were occupied even after the
             * booking expired — orphan rows could survive until `parking:deactivate-expired`
             * eventually flipped status when `end_rent < CURDATE()` (potentially a
             * month later for monthly parking). Soft-delete (`deleted_at = NOW()`) makes
             * the slot release atomically with the transaction expiry.
             *
             * Only un-deleted rows are touched; if a late DOKU payment arrives later,
             * `DokuServiceController::recoverExpiredBookingState` will restore the row.
             */
            $parkingRolledBack = DB::table('t_parking')
                ->where('order_id', $this->orderId)
                ->whereNull('deleted_at')
                ->update([
                    'status'     => 0,
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($parkingRolledBack > 0) {
                Log::info('Parking soft-deleted on booking expiry', [
                    'order_id'          => $this->orderId,
                    'rows_soft_deleted' => $parkingRolledBack,
                ]);
            }

            // Restore voucher usage count if voucher was used
            if ($transaction->voucher_id) {
                $voucher = \App\Models\Voucher::find($transaction->voucher_id);
                if ($voucher && $voucher->current_usage_count > 0) {
                    $voucher->decrement('current_usage_count');
                    Log::info("Restored voucher usage count for voucher_id: {$transaction->voucher_id}");
                }
            }

            // Renewal rollback on expiry — mirrors the cancel flow in
            // BookingController::cancelBooking. When a renewal transaction
            // expires we must reset the parent booking so the user can
            // renew again, otherwise the parent stays flagged as
            // "Diperpanjang" (renewal_status = 1) forever and its
            // check_out_at remains set from the renewal that never paid.
            $txRoomId    = (int)    $transaction->getAttribute('room_id');
            $txUserId    = (int)    $transaction->getAttribute('user_id');
            $txIdrec     = (int)    $transaction->getAttribute('idrec');
            $txCreatedAt = (string) $transaction->getAttribute('created_at');

            if ((int) $transaction->getAttribute('is_renewal') === 1) {
                // Find the most recent previous PAID/CONFIRMED transaction
                // for the same room + user — this is the parent booking
                // that was extended into the now-expired renewal.
                $previousTransaction = DB::table('t_transactions')
                    ->where('room_id', $txRoomId)
                    ->where('user_id', $txUserId)
                    ->where('idrec', '!=', $txIdrec)
                    ->whereRaw('UPPER(transaction_status) IN (?, ?)', ['PAID', 'CONFIRMED'])
                    ->where('created_at', '<', $txCreatedAt)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($previousTransaction) {
                    $prevIdrec   = (int) $previousTransaction->idrec;
                    $prevOrderId = (string) $previousTransaction->order_id;

                    /* Skip rollback if the parent has another successful
                       renewal (paid/confirmed/completed, is_renewal=1) that
                       is newer than this expired one. Without this guard,
                       a late-arriving expiry job for a stale renewal attempt
                       can clobber the renewal_status / check_out_at that a
                       later, paid renewal already set on the parent —
                       making the parent reappear on the Check-Out page. */
                    $hasNewerSuccessfulRenewal = DB::table('t_transactions')
                        ->where('room_id', $txRoomId)
                        ->where('user_id', $txUserId)
                        ->where('idrec', '!=', $txIdrec)
                        ->where('idrec', '!=', $prevIdrec)
                        ->where('is_renewal', 1)
                        ->whereRaw('LOWER(transaction_status) IN (?, ?, ?)', ['paid', 'confirmed', 'completed'])
                        ->where('created_at', '>', $previousTransaction->created_at)
                        ->exists();

                    if ($hasNewerSuccessfulRenewal) {
                        Log::info('Renewal rollback SKIPPED on expiry — newer successful renewal exists', [
                            'expired_transaction_id'  => $txIdrec,
                            'previous_transaction_id' => $prevIdrec,
                            'previous_order_id'       => $prevOrderId,
                        ]);
                    } else {
                        // Reset parent transaction's renewal_status so it can be renewed again
                        DB::table('t_transactions')
                            ->where('idrec', $prevIdrec)
                            ->update(['renewal_status' => 0]);

                        // Clear parent booking's check_out_at (was set when the renewal was created)
                        DB::table('t_booking')
                            ->where('order_id', $prevOrderId)
                            ->update(['check_out_at' => null]);

                        /* rental_status untouched — physical occupancy is owned
                           by check-in / check-out only. An expired renewal doesn't
                           change whether the guest is still in the room. */

                        Log::info('Renewal rollback on expiry', [
                            'expired_transaction_id'  => $txIdrec,
                            'previous_transaction_id' => $prevIdrec,
                            'previous_order_id'       => $prevOrderId,
                        ]);
                    }
                }
            }

            DB::commit();

            // Send push notifications for booking expiry
            try {
                $firebaseService = new FirebaseNotificationService();

                // Notify guest
                $guestUser = $transaction->user_id ? User::find($transaction->user_id) : null;
                if ($guestUser) {
                    $firebaseService->sendToUser(
                        $guestUser,
                        'Booking Expired',
                        "Your booking {$this->orderId} has expired due to incomplete payment.",
                        ['type' => 'booking_expired', 'order_id' => $this->orderId]
                    );
                }

                // Notify admins
                $userName = $booking ? $booking->user_name : 'Guest';
                $firebaseService->sendToAdmins(
                    'Booking Expired',
                    "{$userName}'s booking {$this->orderId} has expired.",
                    ['type' => 'booking_expired', 'order_id' => $this->orderId]
                );
            } catch (\Exception $e) {
                Log::warning('Push notification failed for booking expiry', ['error' => $e->getMessage()]);
            }

            Log::info("Successfully expired booking for order_id: {$this->orderId}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to expire booking for order_id: {$this->orderId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Rethrow the exception to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("ExpireBooking job failed permanently for order_id: {$this->orderId}", [
            'error' => $exception->getMessage()
        ]);
    }
}
