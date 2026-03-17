<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomPrices;
use App\Models\RoomPricingRule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * <!-- Multi-Tier Pricing: Backfill seeder for existing daily rooms -->
 * <!-- For each daily room: creates weekday + weekend pricing rules from existing price_original_daily -->
 * <!-- Sets price_weekday = price_weekend = price_original_daily (same price, since no distinction existed before) -->
 * <!-- Updates existing m_room_prices entries with correct price_type based on day of week (Sat/Sun = weekend) -->
 * <!-- Does NOT change any existing price values — only adds metadata -->
 */
class BackfillPricingRulesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Multi-Tier Pricing backfill...');

        /* Find all rooms that have daily pricing enabled */
        $dailyRooms = Room::where(function ($q) {
            $q->where('periode_daily', 1)
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('price_original_daily')
                     ->where('price_original_daily', '>', 0);
              });
        })->get();

        $this->command->info("Found {$dailyRooms->count()} daily room(s) to backfill.");

        foreach ($dailyRooms as $room) {
            $dailyPrice = (float) $room->price_original_daily;

            if ($dailyPrice <= 0) {
                $this->command->warn("  Room #{$room->idrec} ({$room->name}): skipped — no daily price set.");
                continue;
            }

            /* 1. Set price_weekday and price_weekend on the room (same value for backward compat) */
            $room->update([
                'price_weekday' => $dailyPrice,
                'price_weekend' => $dailyPrice,
            ]);

            /* 2. Create weekday + weekend pricing rules */
            RoomPricingRule::updateOrCreate(
                ['room_id' => $room->idrec, 'rule_type' => 'weekday'],
                ['price' => $dailyPrice, 'status' => 1, 'created_by' => 1]
            );

            RoomPricingRule::updateOrCreate(
                ['room_id' => $room->idrec, 'rule_type' => 'weekend'],
                ['price' => $dailyPrice, 'status' => 1, 'created_by' => 1]
            );

            /* 3. Update existing m_room_prices entries with correct price_type */
            $updated = 0;
            $prices = RoomPrices::where('room_id', $room->idrec)->get();

            foreach ($prices as $priceEntry) {
                $date = Carbon::parse($priceEntry->date);
                $type = $date->isWeekend() ? 'weekend' : 'weekday';

                /* Only update price_type if it's null or still default — don't overwrite 'manual' */
                if (empty($priceEntry->price_type) || $priceEntry->price_type === 'weekday') {
                    $priceEntry->update(['price_type' => $type]);
                    $updated++;
                }
            }

            $this->command->info("  Room #{$room->idrec} ({$room->name}): price={$dailyPrice}, rules created, {$updated} price entries updated.");
        }

        $this->command->info('Backfill complete.');
    }
}
