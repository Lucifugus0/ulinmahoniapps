<?php

namespace App\Services;

use App\Models\CalendarDate;
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

        /* Load per-room pricing rules (prices only — date classification is global) */
        $rules = RoomPricingRule::where('room_id', $roomId)
            ->active()
            ->get()
            ->groupBy('rule_type');

        $weekdayRule = $rules->get('weekday', collect())->first();
        $weekendRule = $rules->get('weekend', collect())->first();
        /* Per-room prices for global date types (price only, no date ranges) */
        $highSeasonPrice = optional($rules->get('high_season', collect())->first())->price;
        $lowSeasonPrice = optional($rules->get('low_season', collect())->first())->price;
        $holidayPrice = optional($rules->get('holiday', collect())->first())->price;

        /* Load global date classifications from m_calendar_dates */
        $calendarDates = CalendarDate::active()
            ->forDateRange($from->toDateString(), $to->toDateString())
            ->pluck('date_type', 'date')
            ->mapWithKeys(fn($type, $date) => [Carbon::parse($date)->toDateString() => $type])
            ->toArray();

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

            /* Resolve price using global calendar + per-room prices */
            $resolved = $this->resolvePrice(
                $current,
                $weekdayPrice,
                $weekendPrice,
                $calendarDates,
                $highSeasonPrice ? (float) $highSeasonPrice : null,
                $lowSeasonPrice ? (float) $lowSeasonPrice : null,
                $holidayPrice ? (float) $holidayPrice : null
            );

            $upsertData[] = [
                'room_id' => $roomId,
                'date' => $dateStr,
                'price' => $resolved['price'],
                'price_type' => $resolved['type'],
                'status' => 1,
                'created_by' => auth()->id(),
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
     * <!-- Resolve the price for a single date using global calendar + per-room prices -->
     * <!-- Priority: 1. high_season  2. low_season  3. holiday  4. weekend (Sat/Sun)  5. weekday -->
     * <!-- Date classification comes from global m_calendar_dates; prices come from per-room rules -->
     *
     * @param array       $calendarDates    Map of dateString => date_type from global calendar
     * @param float|null  $highSeasonPrice  Per-room high season price (null = no override, use weekday/weekend)
     * @param float|null  $lowSeasonPrice   Per-room low season price
     * @param float|null  $holidayPrice     Per-room holiday price
     * @return array{price: float, type: string}
     */
    private function resolvePrice(
        Carbon $date,
        float $weekdayPrice,
        float $weekendPrice,
        array $calendarDates,
        ?float $highSeasonPrice,
        ?float $lowSeasonPrice,
        ?float $holidayPrice
    ): array {
        $dateStr = $date->toDateString();

        /* Check global calendar classification for this date */
        $globalType = $calendarDates[$dateStr] ?? null;

        /* 1. High season — use room's high season price if set, else fall through */
        if ($globalType === 'high_season' && $highSeasonPrice !== null) {
            return ['price' => $highSeasonPrice, 'type' => 'high_season'];
        }

        /* 2. Low season — use room's low season price if set, else fall through */
        if ($globalType === 'low_season' && $lowSeasonPrice !== null) {
            return ['price' => $lowSeasonPrice, 'type' => 'low_season'];
        }

        /* 3. Holiday — use room's holiday price if set, else fall through */
        if ($globalType === 'holiday' && $holidayPrice !== null) {
            return ['price' => $holidayPrice, 'type' => 'holiday'];
        }

        /* 4. Weekend (Friday = 5, Saturday = 6 in Carbon) */
        if ($date->dayOfWeek === Carbon::FRIDAY || $date->dayOfWeek === Carbon::SATURDAY) {
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
                    'created_by' => $row['created_by'] ?? auth()->id(),
                    'updated_at' => $row['updated_at'],
                    'updated_by' => $row['updated_by'],
                ]
            );
        }
    }

    /**
     * <!-- Create default pricing rules for all 5 categories (weekday, weekend, holiday, high_season, low_season) -->
     * <!-- Called when a daily room is created/updated to initialize its pricing rules -->
     */
    public function createDefaultRules(
        int $roomId,
        float $weekdayPrice,
        float $weekendPrice,
        ?float $holidayPrice = null,
        ?float $highSeasonPrice = null,
        ?float $lowSeasonPrice = null,
        ?int $userId = null
    ): void {
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

        /* Create holiday rule if price provided */
        if ($holidayPrice !== null) {
            RoomPricingRule::updateOrCreate(
                ['room_id' => $roomId, 'rule_type' => 'holiday'],
                [
                    'price' => $holidayPrice,
                    'status' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        /* Create high season rule if price provided */
        if ($highSeasonPrice !== null) {
            RoomPricingRule::updateOrCreate(
                ['room_id' => $roomId, 'rule_type' => 'high_season'],
                [
                    'price' => $highSeasonPrice,
                    'status' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        /* Create low season rule if price provided */
        if ($lowSeasonPrice !== null) {
            RoomPricingRule::updateOrCreate(
                ['room_id' => $roomId, 'rule_type' => 'low_season'],
                [
                    'price' => $lowSeasonPrice,
                    'status' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }
    }
}
