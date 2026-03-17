<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Services\RoomPriceGeneratorService;
use Illuminate\Console\Command;

/**
 * <!-- Multi-Tier Pricing: Artisan command to extend m_room_prices entries for daily rooms -->
 * <!-- Ensures per-date prices exist for the next 365 days based on pricing rules -->
 * <!-- Should be run daily via cron: php artisan room-prices:extend -->
 */
class ExtendRoomPricesCommand extends Command
{
    protected $signature = 'room-prices:extend';
    protected $description = 'Extend m_room_prices entries to cover the next 365 days for all daily rooms';

    public function handle(RoomPriceGeneratorService $priceGenerator): int
    {
        $this->info('Extending room prices...');

        /* Find all rooms with daily pricing enabled */
        $dailyRooms = Room::where('periode_daily', 1)
            ->where('status', 1)
            ->get();

        $this->info("Found {$dailyRooms->count()} daily room(s).");

        $totalDates = 0;

        foreach ($dailyRooms as $room) {
            $count = $priceGenerator->regenerateDailyPrices($room->idrec);
            $totalDates += $count;
            $this->line("  Room #{$room->idrec} ({$room->name}): {$count} dates processed");
        }

        $this->info("Done. Total dates processed: {$totalDates}");

        return Command::SUCCESS;
    }
}
