<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
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
    protected $signature = 'bookings:reset-expired-rooms {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset rental_status to 0 for rooms with expired bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to check for expired bookings...');

        try {
            DB::beginTransaction();

            // Find all active bookings with expired transactions
            $expiredBookings = Booking::with(['transaction', 'room'])
                ->where('status', 1) // Active bookings
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

            $processedRooms = [];
            $updatedBookings = 0;
            $updatedRooms = 0;

            foreach ($expiredBookings as $booking) {
                // Update booking status to inactive
                $booking->status = 0;
                $booking->save();
                $updatedBookings++;

                $this->line("- Updated booking {$booking->order_id} to inactive");

                // Check if room needs rental_status reset
                if ($booking->room_id && !in_array($booking->room_id, $processedRooms)) {
                    // Check if there are other active bookings for this room
                    $hasOtherActiveBooking = Booking::where('room_id', $booking->room_id)
                        ->where('status', 1)
                        ->whereHas('transaction', function ($q) {
                            $q->where('transaction_status', '!=', 'expired');
                        })
                        ->exists();

                    if (!$hasOtherActiveBooking) {
                        $room = Room::find($booking->room_id);
                        if ($room && $room->rental_status == 1) {
                            $room->rental_status = 0;
                            $room->save();
                            $updatedRooms++;

                            $roomName = $room->name . ' No. ' . $room->no;
                            $this->line("  → Reset rental_status for room: {$roomName}");
                        }
                    }

                    $processedRooms[] = $booking->room_id;
                }
            }

            DB::commit();

            // Log the operation
            Log::info('Reset expired booking rooms completed', [
                'updated_bookings' => $updatedBookings,
                'updated_rooms' => $updatedRooms,
                'timestamp' => now()
            ]);

            $this->newLine();
            $this->info("✓ Successfully processed:");
            $this->line("  - {$updatedBookings} booking(s) set to inactive");
            $this->line("  - {$updatedRooms} room(s) rental_status reset to 0");

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
