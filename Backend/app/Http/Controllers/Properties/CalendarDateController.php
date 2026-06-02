<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\CalendarDate;
use App\Models\Room;
use App\Services\RoomPriceGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * <!-- Global Calendar: CRUD controller for m_calendar_dates -->
 * <!-- Manages global date classifications (holiday, high_season, low_season) applied to ALL rooms -->
 * <!-- After any change, triggers price regeneration for all daily rooms -->
 */
class CalendarDateController extends Controller
{
    protected RoomPriceGeneratorService $priceGenerator;

    public function __construct(RoomPriceGeneratorService $priceGenerator)
    {
        $this->priceGenerator = $priceGenerator;
    }

    /**
     * <!-- Render the Master Calendar admin page -->
     */
    public function index()
    {
        return view('pages.Properties.calendar.index');
    }

    /**
     * <!-- JSON endpoint: return calendar entries for a given month -->
     * <!-- Supports show_all param to include inactive entries -->
     * <!-- For the active entries list, supports mode=list to show from today onwards -->
     */
    public function getEntries(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $showAll = $request->boolean('show_all', false);
        $mode = $request->input('mode', 'calendar'); // 'calendar' or 'list'

        $query = CalendarDate::with('createdBy:id,username');

        if ($mode === 'list') {
            // List mode: show entries from today onwards (or all if show_all)
            if (!$showAll) {
                $query->where('date', '>=', now()->toDateString());
            }
            // Only show active entries unless show_all is checked
            if (!$showAll) {
                $query->active();
            }
        } else {
            // Calendar mode: show entries for the selected month
            $from = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $to = $from->copy()->endOfMonth();
            $query->forDateRange($from->toDateString(), $to->toDateString());
            // Calendar always shows only active entries
            $query->active();
        }

        $entries = $query->orderBy('date')
            ->get(['idrec', 'date', 'date_type', 'label', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'])
            ->map(function ($entry) {
                $entry->creator_name = $entry->createdBy->username ?? 'System';
                $entry->updater_name = $entry->updatedBy->username ?? null;
                $entry->created_date = $entry->created_at ? $entry->created_at->format('d M Y H:i') : null;
                $entry->updated_date = $entry->updated_at ? $entry->updated_at->format('d M Y H:i') : null;
                return $entry;
            });

        return response()->json([
            'status' => 'success',
            'data' => $entries,
        ]);
    }

    /**
     * <!-- Store a date range: expands into individual m_calendar_dates rows -->
     * <!-- Rejects if any date in the range has an active entry (no overwriting) -->
     * <!-- After saving, regenerates prices for ALL daily rooms -->
     */
    public function storeRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'date_type' => 'required|string|in:holiday,high_season,low_season',
            'label' => 'required|string|min:5|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $start = Carbon::parse($data['date_start']);
        $end = Carbon::parse($data['date_end']);

        // Check for conflicting active entries in the date range
        $conflicts = CalendarDate::active()
            ->forDateRange($start->toDateString(), $end->toDateString())
            ->get(['date', 'date_type', 'label']);

        if ($conflicts->isNotEmpty()) {
            $conflictDates = $conflicts->map(fn($c) => $c->date->format('d M Y') . ' (' . $c->label . ')')->implode(', ');
            return response()->json([
                'status' => 'error',
                'message' => "Tanggal berikut sudah memiliki entri aktif: {$conflictDates}. Nonaktifkan terlebih dahulu.",
            ], 409);
        }

        $count = 0;

        // Expand date range into individual rows — reuse inactive rows if they exist
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            CalendarDate::updateOrCreate(
                ['date' => $date->toDateString()],
                [
                    'date_type' => $data['date_type'],
                    'label' => $data['label'],
                    'status' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );
            $count++;
        }

        // Regenerate prices for all daily rooms
        $this->regenerateAllRoomPrices();

        Log::info("CalendarDate: Added {$count} dates as {$data['date_type']}", $data);

        return response()->json([
            'status' => 'success',
            'message' => "{$count} tanggal berhasil disimpan sebagai " . ucfirst(str_replace('_', ' ', $data['date_type'])),
        ]);
    }

    /**
     * <!-- Toggle status of calendar date entries (soft delete — active/inactive) -->
     */
    /**
     * <!-- Toggle ALL entries matching date_type + label (not just visible range) -->
     */
    public function toggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_type' => 'required|string|in:holiday,high_season,low_season',
            'label' => 'required|string',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $newStatus = $request->status ? 1 : 0;

        // Toggle ALL dates with this type + label, not just the visible range
        CalendarDate::where('date_type', $request->date_type)
            ->where('label', $request->label)
            ->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

        // Regenerate prices for all daily rooms
        $this->regenerateAllRoomPrices();

        return response()->json([
            'status' => 'success',
            'message' => $newStatus ? 'Entri berhasil diaktifkan' : 'Entri berhasil dinonaktifkan',
        ]);
    }

    /**
     * <!-- Manually trigger price regeneration for ALL daily rooms -->
     */
    public function regenerateAll()
    {
        $count = $this->regenerateAllRoomPrices();

        return response()->json([
            'status' => 'success',
            'message' => "Harga berhasil di-generate ulang untuk {$count} kamar",
        ]);
    }

    /**
     * <!-- Helper: regenerate prices for all rooms that have daily pricing enabled -->
     */
    private function regenerateAllRoomPrices(): int
    {
        $dailyRooms = Room::where('periode_daily', 1)->where('status', 1)->pluck('idrec');
        $count = 0;

        foreach ($dailyRooms as $roomId) {
            $this->priceGenerator->regenerateDailyPrices($roomId);
            $count++;
        }

        Log::info("CalendarDate: Regenerated prices for {$count} daily rooms");
        return $count;
    }
}
