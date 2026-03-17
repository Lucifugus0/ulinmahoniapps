<?php

namespace App\Http\Controllers\Bookings\Completed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Illuminate\Support\Carbon;

class CompletedController extends Controller
{
    public function index(Request $request)
    {
        // Show completed bookings:
        // 1. Checked-out bookings (paid, check_out_at NOT NULL)
        // 2. Cancelled bookings (exclude expired)
        $query = Booking::with(['user', 'room', 'property', 'transaction', 'refund'])
            ->where(function ($q) {
                // Checked-out bookings (status=0 after checkout)
                $q->where(function ($checkedOut) {
                    $checkedOut->whereNotNull('check_out_at')
                        ->whereHas('transaction', function ($t) {
                            $t->where('transaction_status', 'paid');
                        });
                })
                // OR cancelled bookings (exclude expired)
                ->orWhere(function ($cancelled) {
                    $cancelled->whereHas('transaction', function ($t) {
                        $t->whereIn('transaction_status', ['canceled', 'cancelled']);
                    });
                });
            });

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Apply date filter only if user provides dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date === $request->end_date) {
                $query->whereDate('created_at', $request->start_date);
            } else {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }
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

        $bookings = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 8));

        return view('pages.bookings.completed.index', compact('bookings'));
    }

    public function filter(Request $request)
    {
        // Show completed bookings:
        // 1. Checked-out bookings (paid, check_out_at NOT NULL)
        // 2. Cancelled bookings (exclude expired)
        $query = Booking::with(['user', 'room', 'property', 'transaction', 'refund'])
            ->where(function ($q) {
                // Checked-out bookings (status=0 after checkout)
                $q->where(function ($checkedOut) {
                    $checkedOut->whereNotNull('check_out_at')
                        ->whereHas('transaction', function ($t) {
                            $t->where('transaction_status', 'paid');
                        });
                })
                // OR cancelled bookings (exclude expired)
                ->orWhere(function ($cancelled) {
                    $cancelled->whereHas('transaction', function ($t) {
                        $t->whereIn('transaction_status', ['canceled', 'cancelled']);
                    });
                });
            });

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // ✅ Filter pencarian
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

        // Apply date filter only if user provides dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            if ($request->start_date === $request->end_date) {
                $query->whereDate('created_at', $request->start_date);
            } else {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }
        }

        $bookings = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 8));

        return response()->json([
            'table' => view('pages.bookings.allbookings.partials.allbookings_table', [
                'bookings' => $bookings,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $bookings->appends($request->input())->links()->toHtml()
        ]);
    }
}
