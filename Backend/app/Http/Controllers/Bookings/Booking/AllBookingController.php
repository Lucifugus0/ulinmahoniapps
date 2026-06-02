<?php

namespace App\Http\Controllers\Bookings\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AllBookingController extends Controller
{
    public function index(Request $request)
    {
        /* Server-side sorting: accepts sort_by and sort_dir params from the frontend.
           Default order is Booking ID descending so the newest bookings surface first
           (the order_id format `UMH-{ymd}{rand}{property}` sorts naturally by date). */
        $sortBy = $request->input('sort_by', 'orderid');
        $sortDir = in_array($request->input('sort_dir'), ['asc', 'desc']) ? $request->input('sort_dir') : 'desc';

        /* checkedInByUser / checkedOutByUser eager-loaded so the merged Dates column
           can render "Check-in at ... by <admin>" / "Check-out at ... by <admin>" without
           N+1 queries. Both come from t_booking.checked_in_by / checked_out_by FKs. */
        $query = Booking::with(['user', 'room', 'property', 'transaction', 'payment.verifiedBy', 'refund.requestedBy', 'checkedInByUser', 'checkedOutByUser'])
            ->latestPerOrder()
            ->whereHas('transaction', function ($q) {
                $q->where('status', 1);
            })
            ->select('t_booking.*')
            ->leftJoin('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->leftJoin('m_properties', 't_booking.property_id', '=', 'm_properties.idrec');

        /* Apply primary sort based on sort_by parameter */
        switch ($sortBy) {
            case 'checkout':
                $query->orderBy('t_transactions.check_out', $sortDir);
                break;
            case 'checkin':
                $query->orderBy('t_transactions.check_in', $sortDir);
                break;
            case 'name':
                $query->orderBy('t_transactions.user_name', $sortDir);
                break;
            case 'property':
                $query->orderBy('m_properties.name', $sortDir);
                break;
            case 'orderid':
            default:
                $query->orderBy('t_booking.order_id', $sortDir);
                break;
        }
        /* Secondary sort for stable ordering */
        if ($sortBy !== 'property') {
            $query->orderBy('m_properties.name', 'asc');
        }
        if ($sortBy !== 'checkin') {
            $query->orderBy('t_transactions.check_in', 'asc');
        }

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            /* Use fully-qualified column to avoid ambiguity with joined tables */
            $query->where('t_booking.property_id', $user->property_id);
        }

        // Apply date filter only if user provides dates
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                if ($startDate === $endDate) {
                    $q->whereDate('check_in', $startDate);
                } else {
                    $q->whereBetween('check_in', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ]);
                }
            });
        }

        // Pencarian berdasarkan order_id atau nama user
        if ($request->filled('search')) {
            $search = $request->search;
            /* Use t_booking.order_id to avoid ambiguity with t_transactions.order_id from leftJoin */
            /* Match against: order_id, linked user's name/username/email/phone, and the transaction's
               denormalized user_name/user_email/user_phone_number — the latter is needed because
               legacy bookings + guest-checkout flows may have NULL user_id but still hold contact
               info on the transaction row. */
            $query->where(function ($q) use ($search) {
                $q->where('t_booking.order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('transaction', function ($q) use ($search) {
                        $q->where('user_name', 'like', "%{$search}%")
                            ->orWhere('user_email', 'like', "%{$search}%")
                            ->orWhere('user_phone_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'pending');
                    });
                    break;
                case 'waiting':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'waiting');
                    });
                    break;
                case 'waiting-check-in':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-in':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNotNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-out':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNotNull('check_in_at')->whereNotNull('check_out_at');
                    break;
                case 'canceled':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'canceled');
                    });
                    break;
                case 'expired':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'expired');
                    });
                    break;
            }
        }

        /* Exclude expired bookings by default unless show_expired checkbox is checked */
        if (!$request->filled('show_expired') && !($request->filled('status') && $request->status === 'expired')) {
            $query->whereHas('transaction', function ($q) {
                $q->where('transaction_status', '!=', 'expired');
            });
        }

        $bookings = $query->paginate($request->input('per_page', 25));

        return view('pages.bookings.allbookings.index', compact('bookings', 'startDate', 'endDate'));
    }

    /**
     * Filter bookings via AJAX — returns JSON with rendered HTML partial.
     * Redirects non-AJAX requests to index to prevent raw JSON on pagination click.
     */
    public function filter(Request $request)
    {
        /* Redirect non-AJAX requests to index to prevent raw JSON display */
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('bookings.index', $request->query());
        }

        /* Server-side sorting: accepts sort_by and sort_dir params from the frontend.
           Default mirrors index() — newest Booking ID first. */
        $sortBy = $request->input('sort_by', 'orderid');
        $sortDir = in_array($request->input('sort_dir'), ['asc', 'desc']) ? $request->input('sort_dir') : 'desc';

        /* checkedInByUser / checkedOutByUser eager-loaded so the merged Dates column
           can render "Check-in at ... by <admin>" / "Check-out at ... by <admin>" without
           N+1 queries. Both come from t_booking.checked_in_by / checked_out_by FKs. */
        $query = Booking::with(['user', 'room', 'property', 'transaction', 'payment.verifiedBy', 'refund.requestedBy', 'checkedInByUser', 'checkedOutByUser'])
            ->latestPerOrder()
            ->whereHas('transaction', function ($q) {
                $q->where('status', 1);
            })
            ->select('t_booking.*')
            ->leftJoin('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->leftJoin('m_properties', 't_booking.property_id', '=', 'm_properties.idrec');

        /* Apply primary sort based on sort_by parameter */
        switch ($sortBy) {
            case 'checkout':
                $query->orderBy('t_transactions.check_out', $sortDir);
                break;
            case 'checkin':
                $query->orderBy('t_transactions.check_in', $sortDir);
                break;
            case 'name':
                $query->orderBy('t_transactions.user_name', $sortDir);
                break;
            case 'property':
                $query->orderBy('m_properties.name', $sortDir);
                break;
            case 'orderid':
            default:
                $query->orderBy('t_booking.order_id', $sortDir);
                break;
        }
        /* Secondary sort for stable ordering */
        if ($sortBy !== 'property') {
            $query->orderBy('m_properties.name', 'asc');
        }
        if ($sortBy !== 'checkin') {
            $query->orderBy('t_transactions.check_in', 'asc');
        }

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            /* Use fully-qualified column to avoid ambiguity with joined tables */
            $query->where('t_booking.property_id', $user->property_id);
        }

        // Apply date filter only if user provides dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                if ($startDate === $endDate) {
                    $q->whereDate('check_in', $startDate);
                } else {
                    $q->whereBetween('check_in', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ]);
                }
            });
        }

        // Search by order_id or user name
        if ($request->filled('search')) {
            $search = $request->search;
            /* Use t_booking.order_id to avoid ambiguity with t_transactions.order_id from leftJoin */
            /* Match against: order_id, linked user's name/username/email/phone, and the transaction's
               denormalized user_name/user_email/user_phone_number — the latter is needed because
               legacy bookings + guest-checkout flows may have NULL user_id but still hold contact
               info on the transaction row. */
            $query->where(function ($q) use ($search) {
                $q->where('t_booking.order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('transaction', function ($q) use ($search) {
                        $q->where('user_name', 'like', "%{$search}%")
                            ->orWhere('user_email', 'like', "%{$search}%")
                            ->orWhere('user_phone_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'pending');
                    });
                    break;
                case 'waiting':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'waiting');
                    });
                    break;
                case 'waiting-check-in':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-in':
                    $query->where('t_booking.status', 1)->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNotNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-out':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNotNull('check_in_at')->whereNotNull('check_out_at');
                    break;
                case 'canceled':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'canceled');
                    });
                    break;
                case 'expired':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'expired');
                    });
                    break;
            }
        }

        /* Exclude expired bookings by default unless show_expired checkbox is checked */
        if (!$request->filled('show_expired') && !($request->filled('status') && $request->status === 'expired')) {
            $query->whereHas('transaction', function ($q) {
                $q->where('transaction_status', '!=', 'expired');
            });
        }

        $bookings = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'table' => view('pages.bookings.allbookings.partials.allbookings_table', [
                'bookings' => $bookings,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $bookings->appends($request->input())->links()->toHtml()
        ]);
    }
}
