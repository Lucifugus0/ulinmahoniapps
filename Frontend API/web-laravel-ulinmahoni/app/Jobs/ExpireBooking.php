<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Payment;
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

            // Restore voucher usage count if voucher was used
            if ($transaction->voucher_id) {
                $voucher = \App\Models\Voucher::find($transaction->voucher_id);
                if ($voucher && $voucher->current_usage_count > 0) {
                    $voucher->decrement('current_usage_count');
                    Log::info("Restored voucher usage count for voucher_id: {$transaction->voucher_id}");
                }
            }

            DB::commit();

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
