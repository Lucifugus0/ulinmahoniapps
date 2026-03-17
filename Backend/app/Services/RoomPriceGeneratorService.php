<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomPrices;
use App\Models\RoomPricingRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * <!-- Multi-Tier Pricing: Core service that resolves pricing rules into per-date prices -->
 * <!-- Called when: room created/updated, pricing rule CRUD, daily cron job -->
 * <!-- Algorithm: For each date, resolve price using priority: high_season > low_season > holiday > weekend > weekday -->
 * <!-- Manual overrides (price_type = 'manual') are always preserved -->
 */
class RoomPriceGeneratorService
{
    /**
     * <!-- Regenerate daily prices in m_room_prices for a room based on its pricing rules -->
     * <!-- Preserves manual overrides. Upserts all other dates with resolved price + price_type -->
     *
     * @param int         $roomId  The m_rooms.idrec
     * @param Carbon|null $from    Start date (default: today)
     * @param Carbon|null $to      End date (default: today + 365 days)
     * @return int Number of dates processed
     */
    public function regenerateDailyPrices(int $roomId, ?Carbon $from = null, ?Carbon $to = null): int
    {
        $from = $from ?? Carbon::today();
        $to = $to ?? Carbon::today()->addDays(365);

        /* Load all active pricing rules for this room */
        $rules = RoomPricingRule::where('room_id', $roomId)
            ->active()
            ->get()
            ->groupBy('rule_type');

        $weekdayRule = $rules->get('weekday', collect())->first();
        $weekendRule = $rules->get('weekend', collect())->first();
        $highSeasonRules = $rules->get('high_season', collect());
        $lowSeasonRules = $rules->get('low_season', collect());
        $holidayRules = $rules->get('holiday', collect());

        /* If no weekday rule exists, fall back to room's price_original_daily */
        $room = Room::find($roomId);
        if (!$room) {
            Log::warning("RoomPriceGeneratorService: Room {$roomId} not found");
            return 0;
        }

        $weekdayPrice = $weekdayRule ? (float) $weekdayRule->price : (float) $room->price_original_daily;
        $weekendPrice = $weekendRule ? (float) $weekendRule->price : $weekdayPrice;

        /* Load existing manual overrides so we can preserve them */
        $manualOverrides = RoomPrices::where('room_id', $roomId)
            ->where('price_type', 'manual')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->pluck('price', 'date')
            ->toArray();

        /* Generate prices for each date in range */
        $upsertData = [];
        $current = $from->copy();
        $processedCount = 0;

        while ($current->lte($to)) {
            $dateStr = $current->toDateString();
            $processedCount++;

            /* Skip manual overrides — they are preserved as-is */
            if (isset($manualOverrides[$dateStr])) {
                $current->addDay();
                continue;
            }

            /* Resolve price using priority: high_season > low_season > holiday > weekend > weekday */
            $resolved = $this->resolvePrice(
                $current,
                $weekdayPrice,
                $weekendPrice,
                $highSeasonRules,
                $lowSeasonRules,
                $holidayRules
            );

            $upsertData[] = [
                'room_id' => $roomId,
                'date' => $dateStr,
                'price' => $resolved['price'],
                'price_type' => $resolved['type'],
                'status' => 1,
                'updated_at' => now(),
                'updated_by' => auth()->id(),
            ];

            /* Batch upsert every 100 rows for performance */
            if (count($upsertData) >= 100) {
                $this->batchUpsert($upsertData);
                $upsertData = [];
            }

            $current->addDay();
        }

        /* Flush remaining rows */
        if (!empty($upsertData)) {
            $this->batchUpsert($upsertData);
        }

        Log::info("RoomPriceGeneratorService: Regenerated {$processedCount} dates for room {$roomId}");

        return $processedCount;
    }

    /**
     * <!-- Resolve the price for a single date based on pricing rules priority -->
     * <!-- Priority: 1. high_season  2. low_season  3. holiday  4. weekend (Sat/Sun)  5. weekday -->
     *
     * @return array{price: float, type: string}
     */
    private function resolvePrice(
        Carbon $date,
        float $weekdayPrice,
        float $weekendPrice,
        $highSeasonRules,
        $lowSeasonRules,
        $holidayRules
    ): array {
        $dateStr = $date->toDateString();

        /* 1. Check high season — overrides all other prices for the date range */
        foreach ($highSeasonRules as $rule) {
            if ($dateStr >= $rule->date_start->toDateString() && $dateStr <= $rule->date_end->toDateString()) {
                return ['price' => (float) $rule->price, 'type' => 'high_season'];
            }
        }

        /* 2. Check low season — overrides weekday/weekend/holiday prices */
        foreach ($lowSeasonRules as $rule) {
            if ($dateStr >= $rule->date_start->toDateString() && $dateStr <= $rule->date_end->toDateString()) {
                return ['price' => (float) $rule->price, 'type' => 'low_season'];
            }
        }

        /* 3. Check public holiday — applies if not in a season */
        foreach ($holidayRules as $rule) {
            if ($dateStr >= $rule->date_start->toDateString() && $dateStr <= $rule->date_end->toDateString()) {
                return ['price' => (float) $rule->price, 'type' => 'holiday'];
            }
        }

        /* 4. Weekend (Saturday = 6, Sunday = 0 in Carbon) */
        if ($date->isWeekend()) {
            return ['price' => $weekendPrice, 'type' => 'weekend'];
        }

        /* 5. Weekday (default) */
        return ['price' => $weekdayPrice, 'type' => 'weekday'];
    }

    /**
     * <!-- Batch upsert into m_room_prices — update if (room_id, date) exists, insert otherwise -->
     */
    private function batchUpsert(array $rows): void
    {
        foreach ($rows as $row) {
            RoomPrices::updateOrCreate(
                [
                    'room_id' => $row['room_id'],
                    'date' => $row['date'],
                ],
                [
                    'price' => $row['price'],
                    'price_type' => $row['price_type'],
                    'status' => $row['status'],
                    'updated_at' => $row['updated_at'],
                    'updated_by' => $row['updated_by'],
                ]
            );
        }
    }

    /**
     * <!-- Create default weekday + weekend pricing rules for a room -->
     * <!-- Called when a daily room is created to initialize its pricing rules -->
     */
    public function createDefaultRules(int $roomId, float $weekdayPrice, float $weekendPrice, ?int $userId = null): void
    {
        /* Create weekday rule */
        RoomPricingRule::updateOrCreate(
            ['room_id' => $roomId, 'rule_type' => 'weekday'],
            [
                'price' => $weekdayPrice,
                'status' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        /* Create weekend rule */
        RoomPricingRule::updateOrCreate(
            ['room_id' => $roomId, 'rule_type' => 'weekend'],
            [
                'price' => $weekendPrice,
                'status' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );
    }
}
