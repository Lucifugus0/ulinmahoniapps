<?php

namespace App\Http\Controllers\Bookings\ModifyBooking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Modify Booking — admin tool for changing the scheduled check_in / check_out / paid_at
 * on a paid booking AFTER it's been confirmed but typically BEFORE check-out.
 *
 * Filter set is identical to Confirmed Bookings (paid + active + not yet checked out +
 * latestPerOrder + property scope for site users + optional date-range / search).
 *
 * Audit trail uses Option A: each modify event clones the active t_booking row, sets the
 * old row to status=0, points the clone's previous_booking_id at the old row, and stamps
 * the clone with modified_at / modified_by / modification_type / modification_notes. The
 * actual scheduled-date / paid-at changes are written to t_transactions; t_booking is the
 * audit ledger.
 *
 * paid_at is editable ONLY when transaction_type indicates BRI Manual (web 'bri_manual'
 * or app 'BRI Manual'). DOKU-channel transactions are immutable for paid_at because the
 * gateway controls that timestamp via webhook.
 */
class ModifyBookingController extends Controller
{
    /**
     * BRI Manual transaction_type values that allow paid_at editing. The web flow stores
     * lowercase 'bri_manual'; the legacy mobile app stored mixed-case 'BRI Manual'.
     */
    private const PAID_AT_EDITABLE_TYPES = ['bri_manual', 'BRI Manual'];

    public function index()
    {
        $perPage = request('per_page', 25);
        $bookings = $this->buildQuery()->paginate($perPage);

        return view('pages.bookings.modify.index', [
            'bookings' => $bookings,
            'per_page' => $perPage,
        ]);
    }

    public function filter(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('modifyBooking.index', $request->query());
        }

        $bookings = $this->buildQuery()->paginate($request->input('per_page', 25));

        return response()->json([
            'table' => view('pages.bookings.modify.partials.modify_table', [
                'bookings' => $bookings,
                'per_page' => $request->input('per_page', 25),
            ])->render(),
            'pagination' => $bookings->appends($request->input())->links()->toHtml(),
        ]);
    }

    /**
     * Mirrors NewReservController::filterBookings() but renamed for clarity.
     */
    private function buildQuery()
    {
        $query = Booking::with([
                'user', 'room', 'property', 'transaction',
                'checkedInByUser', 'checkedOutByUser', 'modifiedByUser',
            ])
            ->latestPerOrder()
            ->where('t_booking.status', 1)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNull('check_out_at')
            ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->orderByRaw('ISNULL(check_in_at) DESC')
            ->orderBy('t_transactions.check_in', 'desc');

        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('t_booking.property_id', $user->property_id);
        }

        if (request()->filled('start_date') && request()->filled('end_date')) {
            $startDate = request('start_date');
            $endDate = request('end_date');
            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                if ($startDate === $endDate) {
                    $q->whereDate('check_in', $startDate);
                } else {
                    $q->whereBetween('check_in', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59',
                    ]);
                }
            });
        }

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('t_booking.order_id', 'like', "%{$search}%")
                    ->orWhere('t_booking.user_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('transaction', function ($q) use ($search) {
                        $q->where('user_email', 'like', "%{$search}%")
                            ->orWhere('user_phone_number', 'like', "%{$search}%");
                    });
            });
        }

        return $query->select('t_booking.*');
    }

    /**
     * Return JSON with the current booking + transaction snapshot + modification history.
     * Used by the modal's open() handler to populate fields and the history panel.
     */
    public function show($order_id)
    {
        $booking = Booking::with(['transaction', 'property', 'room', 'user'])
            ->where('order_id', $order_id)
            ->where('status', 1)
            ->firstOrFail();

        /* Modification history: walk t_booking rows for the same order_id where modified_at
           is set, newest first. Each row is a snapshot AFTER its modification was applied. */
        $history = Booking::where('order_id', $order_id)
            ->whereNotNull('modified_at')
            ->with('modifiedByUser')
            ->orderByDesc('modified_at')
            ->get()
            ->map(function ($row) {
                $admin = $row->modifiedByUser;
                $adminName = $admin
                    ? (trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')) ?: ($admin->username ?? '-'))
                    : '-';
                return [
                    'modified_at' => $row->modified_at?->format('Y-m-d H:i:s'),
                    'modified_by' => $adminName,
                    'modification_type' => $row->modification_type,
                    'modification_notes' => $row->modification_notes,
                ];
            });

        $tx = $booking->transaction;
        $paidAtEditable = in_array($tx?->transaction_type ?? '', self::PAID_AT_EDITABLE_TYPES, true);

        return response()->json([
            'order_id' => $booking->order_id,
            'guest_name' => trim(($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '')) ?: ($tx?->user_name ?? '-'),
            'guest_email' => $tx?->user_email ?? $booking->user_email ?? $booking->user?->email ?? '-',
            'guest_phone' => $tx?->user_phone_number ?? $booking->user_phone_number ?? $booking->user?->phone_number ?? '-',
            'property_name' => $booking->property?->name ?? '-',
            'room_name' => $booking->room?->name ?? '-',
            'room_no' => $booking->room?->no ?? '-',
            'transaction_type' => $tx?->transaction_type ?? null,
            'paid_at_editable' => $paidAtEditable,
            /* Dates returned as YYYY-MM-DD only (no time) — the modal exposes <input type="date">
               for check_in / check_out so admins can't accidentally shift the standard 14:00
               check-in / 12:00 check-out times. paid_at stays as datetime-local since it's a
               literal payment timestamp, not a recurring policy time. */
            'check_in' => $tx?->check_in?->format('Y-m-d'),
            'check_out' => $tx?->check_out?->format('Y-m-d'),
            'paid_at' => $tx?->paid_at ? Carbon::parse($tx->paid_at)->format('Y-m-d\TH:i') : null,
            'history' => $history,
        ]);
    }

    /**
     * Apply the modification: validate, update t_transactions, clone the active t_booking row.
     * Wraps the writes in a DB transaction so a partial failure doesn't leave the booking with
     * an updated transaction but no audit row (or vice versa).
     */
    public function modify(Request $request, $order_id)
    {
        $validated = $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'paid_at' => 'nullable|date',
            'modification_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $booking = Booking::where('order_id', $order_id)
                ->where('status', 1)
                ->firstOrFail();

            $tx = Transaction::where('order_id', $order_id)->firstOrFail();

            $oldCheckIn = $tx->check_in?->format('Y-m-d H:i:s');
            $oldCheckOut = $tx->check_out?->format('Y-m-d H:i:s');
            $oldPaidAt = $tx->paid_at ? Carbon::parse($tx->paid_at)->format('Y-m-d H:i:s') : null;

            /* Modal sends check_in / check_out as YYYY-MM-DD only. Combine with the standard
               property times (14:00 check-in, 12:00 check-out) so the time portion stays
               anchored regardless of how admins fiddle with the date inputs — matches the
               same pattern used by Api/BookingController::store(). */
            $newCheckIn = Carbon::createFromFormat('Y-m-d', substr($validated['check_in'], 0, 10))->setTime(14, 0, 0);
            $newCheckOut = Carbon::createFromFormat('Y-m-d', substr($validated['check_out'], 0, 10))->setTime(12, 0, 0);

            $changes = [];
            if ($oldCheckIn !== $newCheckIn->format('Y-m-d H:i:s')) {
                $changes['check_in'] = ['old' => $oldCheckIn, 'new' => $newCheckIn->format('Y-m-d H:i:s')];
            }
            if ($oldCheckOut !== $newCheckOut->format('Y-m-d H:i:s')) {
                $changes['check_out'] = ['old' => $oldCheckOut, 'new' => $newCheckOut->format('Y-m-d H:i:s')];
            }

            $paidAtEditable = in_array($tx->transaction_type ?? '', self::PAID_AT_EDITABLE_TYPES, true);
            $newPaidAt = null;
            if (!empty($validated['paid_at'])) {
                if (!$paidAtEditable) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'paid_at can only be modified for BRI Manual transactions.',
                    ], 422);
                }
                $newPaidAt = Carbon::parse($validated['paid_at']);
                if ($oldPaidAt !== $newPaidAt->format('Y-m-d H:i:s')) {
                    $changes['paid_at'] = ['old' => $oldPaidAt, 'new' => $newPaidAt->format('Y-m-d H:i:s')];
                }
            }

            if (empty($changes)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No changes detected. Provide at least one different value.',
                ], 422);
            }

            /* Apply the t_transactions changes. Only assign paid_at when it actually changed
               so we don't accidentally clobber a NULL with a parsed value on every save. */
            $tx->check_in = $newCheckIn;
            $tx->check_out = $newCheckOut;
            if ($paidAtEditable && isset($changes['paid_at'])) {
                $tx->paid_at = $newPaidAt;
            }
            $tx->save();

            /* Audit clone: deactivate the parent t_booking row and create a new active row that
               points back to it via previous_booking_id. The new row inherits everything else
               (room_id, check_in_at, check_out_at, ktp_img, doc_path, etc.) from the parent so
               the rest of the system continues to see consistent booking state. */
            $oldStatus = $booking->status;
            $oldIdrec = $booking->idrec;

            $modificationType = collect($changes)->keys()->map(function ($k) {
                return $k === 'paid_at' ? 'payment_date' : 'date';
            })->unique()->sort()->values()->implode('+');
            // 'date' / 'payment_date' / 'date+payment_date'

            $cloneData = $booking->only([
                'order_id', 'room_id', 'user_name', 'user_email', 'user_phone_number',
                'property_id', 'check_in_at', 'checked_in_by', 'doc_type', 'doc_path',
                'check_out_at', 'checked_out_by', 'created_by',
            ]);
            $cloneData['updated_by'] = Auth::id();
            $cloneData['status'] = 1;
            $cloneData['previous_booking_id'] = $oldIdrec;
            $cloneData['modified_at'] = now();
            $cloneData['modified_by'] = Auth::id();
            $cloneData['modification_type'] = $modificationType;
            $cloneData['modification_notes'] = $validated['modification_notes'] ?? null;

            // Deactivate the parent first so latestPerOrder() picks up only the clone.
            $booking->status = 0;
            $booking->save();

            Booking::create($cloneData);

            DB::commit();

            Log::info('Booking modified', [
                'order_id' => $order_id,
                'modified_by' => Auth::id(),
                'modification_type' => $modificationType,
                'changes' => $changes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking modified successfully.',
                'changes' => $changes,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Modify Booking failed', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to modify booking: ' . $e->getMessage(),
            ], 500);
        }
    }
}
