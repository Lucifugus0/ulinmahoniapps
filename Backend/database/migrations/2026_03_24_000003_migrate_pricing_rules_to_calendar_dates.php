<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Migrate existing per-room holiday/season date ranges from m_room_pricing_rules
     * into the global m_calendar_dates table, then nullify the date columns on the source rows.
     */
    public function up(): void
    {
        // Priority: high_season > low_season > holiday (higher priority wins on conflict)
        $priorityOrder = ['high_season', 'low_season', 'holiday'];

        foreach ($priorityOrder as $ruleType) {
            $rules = DB::table('m_room_pricing_rules')
                ->where('rule_type', $ruleType)
                ->whereNotNull('date_start')
                ->whereNotNull('date_end')
                ->where('status', 1)
                ->get();

            foreach ($rules as $rule) {
                $start = Carbon::parse($rule->date_start);
                $end = Carbon::parse($rule->date_end);

                // Expand date range into individual rows
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    DB::table('m_calendar_dates')->insertOrIgnore([
                        'date' => $date->toDateString(),
                        'date_type' => $ruleType,
                        'label' => $rule->label,
                        'status' => 1,
                        'created_by' => $rule->created_by,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Nullify date columns on migrated rules (keep the price)
            DB::table('m_room_pricing_rules')
                ->where('rule_type', $ruleType)
                ->whereNotNull('date_start')
                ->update([
                    'date_start' => null,
                    'date_end' => null,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse is not practical — data migration is one-way.
     */
    public function down(): void
    {
        // Cannot reliably reverse: original per-room date ranges are lost
        DB::table('m_calendar_dates')->truncate();
    }
};
