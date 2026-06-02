<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetExpiredBookingRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:reset-expired-rooms {--force : Force run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate t_booking rows whose transaction has expired (sets t_booking.status = 0).';

    /**
     * Execute the console command.
     *
     * Per the rental_status rule: physical occupancy is owned exclusively by
     * admin check-in / check-out actions. Booking lifecycle events (expiry,
     * cancellation, renewal, payment) MUST NOT auto-flip m_rooms.rental_status.
     *
     * This command therefore only deactivates expired t_booking rows so the
     * booking lists stop showing them — it does NOT touch m_rooms.rental_status.
     * The check-out path (CheckOutController) is the sole writer that flips
     * a room back to 0.
     */
    public function handle()
    {
        $this->info('Starting to deactivate expired bookings...');

        try {
            DB::beginTransaction();

            $expiredBookings = Booking::with(['transaction'])
                ->where('status', 1)
                ->whereHas('transaction', function ($q) {
                    $q->where('transaction_status', 'expired');
                })
                ->get();

            if ($expiredBookings->isEmpty()) {
                $this->info('No expired bookings found.');
                DB::commit();
                return Command::SUCCESS;
            }

            $this->info("Found {$expiredBookings->count()} expired booking(s).");

            $updatedBookings = 0;

            foreach ($expiredBookings as $booking) {
                $booking->status = 0;
                $booking->save();
                $updatedBookings++;

                $this->line("- Updated booking {$booking->order_id} to inactive");
            }

            DB::commit();

            Log::info('Reset expired booking rooms completed', [
                'updated_bookings' => $updatedBookings,
                'timestamp' => now()
            ]);

            $this->newLine();
            $this->info("✓ Successfully processed:");
            $this->line("  - {$updatedBookings} booking(s) set to inactive");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Error processing expired bookings: ' . $e->getMessage());
            Log::error('Reset expired booking rooms failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
