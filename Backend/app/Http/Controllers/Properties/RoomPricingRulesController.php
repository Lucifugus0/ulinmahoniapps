<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomPricingRule;
use App\Services\RoomPriceGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * <!-- Multi-Tier Pricing: CRUD controller for room pricing rules -->
 * <!-- Manages weekday, weekend, holiday, high_season, low_season rules per room -->
 * <!-- After any rule change, triggers RoomPriceGeneratorService to regenerate m_room_prices -->
 */
class RoomPricingRulesController extends Controller
{
    protected RoomPriceGeneratorService $priceGenerator;

    public function __construct(RoomPriceGeneratorService $priceGenerator)
    {
        $this->priceGenerator = $priceGenerator;
    }

    /**
     * <!-- List all pricing rules for a room -->
     */
    public function index(int $roomId)
    {
        $room = Room::findOrFail($roomId);
        $rules = RoomPricingRule::where('room_id', $roomId)
            ->orderByRaw("FIELD(rule_type, 'weekday', 'weekend', 'holiday', 'high_season', 'low_season')")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'room_id' => $roomId,
                'room_name' => $room->name,
                'price_weekday' => $room->price_weekday,
                'price_weekend' => $room->price_weekend,
                'rules' => $rules,
            ],
        ]);
    }

    /**
     * <!-- Create a new pricing rule for a room -->
     */
    public function store(Request $request, int $roomId)
    {
        $room = Room::findOrFail($roomId);

        $validator = Validator::make($request->all(), [
            'rule_type' => 'required|string|in:weekday,weekend,holiday,high_season,low_season',
            'price' => 'required|numeric|min:0',
            'date_start' => 'required_if:rule_type,holiday,high_season,low_season|nullable|date',
            'date_end' => 'required_if:rule_type,holiday,high_season,low_season|nullable|date|after_or_equal:date_start',
            'label' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        /* For weekday/weekend: update existing rule or create new (only 1 per room per type) */
        if (in_array($data['rule_type'], ['weekday', 'weekend'])) {
            $rule = RoomPricingRule::updateOrCreate(
                ['room_id' => $roomId, 'rule_type' => $data['rule_type']],
                [
                    'price' => $data['price'],
                    'status' => 1,
                    'updated_by' => Auth::id(),
                ]
            );

            /* Also update the denormalized column on m_rooms */
            $column = $data['rule_type'] === 'weekday' ? 'price_weekday' : 'price_weekend';
            $room->update([$column => $data['price']]);

            /* Keep price_original_daily in sync with weekday price for backward compat */
            if ($data['rule_type'] === 'weekday') {
                $room->update(['price_original_daily' => $data['price']]);
            }
        } else {
            /* For holiday/season: create a new rule (multiple allowed per room) */
            $rule = RoomPricingRule::create([
                'room_id' => $roomId,
                'rule_type' => $data['rule_type'],
                'price' => $data['price'],
                'date_start' => $data['date_start'],
                'date_end' => $data['date_end'],
                'label' => $data['label'] ?? null,
                'status' => 1,
                'created_by' => Auth::id(),
            ]);
        }

        /* Regenerate per-date prices after rule change */
        $this->priceGenerator->regenerateDailyPrices($roomId);

        Log::info("PricingRule created/updated for room {$roomId}", $rule->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Aturan harga berhasil disimpan',
            'data' => $rule,
        ]);
    }

    /**
     * <!-- Update an existing pricing rule -->
     */
    public function update(Request $request, int $roomId, int $ruleId)
    {
        $rule = RoomPricingRule::where('room_id', $roomId)->where('idrec', $ruleId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'label' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $rule->update(array_merge($data, ['updated_by' => Auth::id()]));

        /* Sync denormalized columns if weekday/weekend changed */
        if ($rule->rule_type === 'weekday') {
            Room::where('idrec', $roomId)->update([
                'price_weekday' => $data['price'],
                'price_original_daily' => $data['price'],
            ]);
        } elseif ($rule->rule_type === 'weekend') {
            Room::where('idrec', $roomId)->update(['price_weekend' => $data['price']]);
        }

        /* Regenerate per-date prices after rule change */
        $this->priceGenerator->regenerateDailyPrices($roomId);

        return response()->json([
            'status' => 'success',
            'message' => 'Aturan harga berhasil diperbarui',
            'data' => $rule,
        ]);
    }

    /**
     * <!-- Delete a pricing rule (only for holiday/season — weekday/weekend cannot be deleted) -->
     */
    public function destroy(int $roomId, int $ruleId)
    {
        $rule = RoomPricingRule::where('room_id', $roomId)->where('idrec', $ruleId)->firstOrFail();

        /* Prevent deleting base weekday/weekend rules */
        if (in_array($rule->rule_type, ['weekday', 'weekend'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aturan harga dasar (weekday/weekend) tidak dapat dihapus. Ubah harganya saja.',
            ], 400);
        }

        $rule->delete();

        /* Regenerate per-date prices after rule deletion */
        $this->priceGenerator->regenerateDailyPrices($roomId);

        return response()->json([
            'status' => 'success',
            'message' => 'Aturan harga berhasil dihapus',
        ]);
    }

    /**
     * <!-- Manually trigger price regeneration for a room -->
     */
    public function regenerate(int $roomId)
    {
        Room::findOrFail($roomId);

        $count = $this->priceGenerator->regenerateDailyPrices($roomId);

        return response()->json([
            'status' => 'success',
            'message' => "Harga berhasil di-generate ulang untuk {$count} hari",
            'data' => ['dates_processed' => $count],
        ]);
    }
}
