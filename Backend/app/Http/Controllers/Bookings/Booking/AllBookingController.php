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
        $query = Booking::with(['user', 'room', 'property', 'transaction'])
            ->where('status', 1) // Only show active bookings (filter out room-changed old records)
            ->orderByDesc('check_in_at');

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
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
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'pending');
                    });
                    break;
                case 'waiting':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'waiting');
                    });
                    break;
                case 'waiting-check-in':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-in':
                    $query->whereHas('transaction', function ($q) {
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

        $bookings = $query->paginate($request->input('per_page', 8));

        return view('pages.bookings.allbookings.index', compact('bookings', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        $query = Booking::with(['user', 'room', 'property', 'transaction'])
            ->where('status', 1) // Only show active bookings (filter out room-changed old records)
            ->orderByDesc('check_in_at');

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
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
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'pending');
                    });
                    break;
                case 'waiting':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'waiting');
                    });
                    break;
                case 'waiting-check-in':
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })->whereNull('check_in_at')->whereNull('check_out_at');
                    break;
                case 'checked-in':
                    $query->whereHas('transaction', function ($q) {
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

        $bookings = $query->paginate($request->input('per_page', 8));

        return response()->json([
            'table' => view('pages.bookings.allbookings.partials.allbookings_table', [
                'bookings' => $bookings,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $bookings->appends($request->input())->links()->toHtml()
        ]);
    }
}
