<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Exports\RentedRoomsReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RentedRoomsReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get properties for filter based on user access
        if ($user->isSuperAdmin()) {
            $properties = Property::where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            $properties = Property::where('status', 1)
                ->where('idrec', $user->property_id)
                ->orderBy('name')
                ->get();
        }

        // Default dates (no default - show all data)
        $selectedDate = '';
        $startDate = '';
        $endDate = '';

        // Set property_id based on user access
        if ($user->isSuperAdmin()) {
            $propertyId = $request->input('property_id');
        } else {
            $propertyId = $user->property_id;
        }

        return view('pages.reports.rented-rooms-report.index', compact(
            'properties',
            'selectedDate',
            'startDate',
            'endDate',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $reportType = $request->input('report_type', 'checked-in');

        $query = Booking::with([
                'transaction',
                'payment',
                'property',
                'room'
            ])
            ->orderByDesc('created_at');

        // Apply report type filter
        switch ($reportType) {
            case 'waiting-check-in':
                // Bookings that are paid but not yet checked in
                // Filter: transaction_status = 'paid' AND check_in_at is NULL
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('transaction_status', 'paid');
                    if ($request->filled('selected_date')) {
                        $selectedDate = $request->selected_date;
                        $q->whereDate('check_in', '<=', $selectedDate)
                          ->whereDate('check_out', '>=', $selectedDate);
                    }
                })->whereNull('check_in_at');
                break;

            case 'checked-in':
                // Bookings that are paid and already checked in
                // Filter: transaction_status = 'paid' AND check_in_at is NOT NULL
                $query->whereNotNull('check_in_at');
                if ($request->filled('selected_date')) {
                    $query->whereDate('check_in_at', $request->selected_date);
                }
                $query->whereHas('transaction', function ($q) {
                    $q->where('transaction_status', 'paid');
                });
                break;

            case 'rooms-occupied':
                // Rooms that are currently occupied
                $query->whereNotNull('check_in_at');
                if ($request->filled('selected_date')) {
                    $selectedDate = $request->selected_date;
                    $query->whereDate('check_in_at', '<=', $selectedDate)
                          ->whereHas('transaction', function ($q) use ($selectedDate) {
                              $q->where('transaction_status', 'paid')
                                ->whereDate('check_out', '>=', $selectedDate);
                          });
                } else {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    });
                }
                break;

            case 'check-out':
                // Bookings that are checked out
                // Filter: check_in_at NOT NULL AND check_out_at NOT NULL
                $query->whereNotNull('check_in_at')
                      ->whereNotNull('check_out_at');
                if ($request->filled('selected_date')) {
                    $query->whereDate('check_out_at', $request->selected_date);
                }
                $query->whereHas('transaction', function ($q) {
                    $q->where('transaction_status', 'paid');
                });
                break;

            case 'cancelled':
                // Cancelled bookings only
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('transaction_status', 'cancelled');
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $q->whereBetween('cancel_at', [
                            $request->start_date . ' 00:00:00',
                            $request->end_date . ' 23:59:59'
                        ]);
                    }
                });
                break;
        }

        // Property filter based on user access
        if ($user->isSuperAdmin()) {
            if ($request->filled('property_id')) {
                $query->where('property_id', $request->property_id);
            }
        } else {
            // Non-super admin: automatically filter by their property
            if ($user->property_id) {
                $query->where('property_id', $user->property_id);
            }
        }

        // Search filter (tenant name, room number, order_id)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->paginate($request->input('per_page', 15));

        // Transform data for display
        $data = $bookings->map(function ($booking, $index) use ($bookings) {
            $transaction = $booking->transaction;

            // Calculate duration from transaction fields
            $duration = '-';
            $days = 0;
            $months = 0;

            if ($transaction) {
                if ($transaction->booking_type === 'daily') {
                    $days = $transaction->booking_days ?? 1;
                    $duration = $days . ' day' . ($days != 1 ? 's' : '');
                } else {
                    $months = $transaction->booking_months ?? 1;
                    $duration = $months . ' month' . ($months != 1 ? 's' : '');
                }
            }

            $offset = ($bookings->currentPage() - 1) * $bookings->perPage();

            return [
                'no' => $offset + $index + 1,
                'property' => $booking->property ? $booking->property->name : '-',
                'tenant_name' => $booking->user_name ?? '-',
                'room_number' => $booking->room ? $booking->room->name : '-',
                'booking_type' => $transaction ? ucfirst($transaction->booking_type) : '-',
                'check_in' => $transaction && $transaction->check_in ?
                    Carbon::parse($transaction->check_in)->format('d M Y') : '-',
                'check_out' => $transaction && $transaction->check_out ?
                    Carbon::parse($transaction->check_out)->format('d M Y') : '-',
                'duration' => $duration,
                'room_price' => 'Rp ' . number_format($transaction->room_price ?? 0, 0, ',', '.'),
                'service_fee' => 'Rp ' . number_format($transaction->service_fees ?? 0, 0, ',', '.'),
                'grand_total' => 'Rp ' . number_format($transaction->grandtotal_price ?? 0, 0, ',', '.'),
                'payment_status' => $transaction ? $transaction->transaction_status : '-',
                'payment_date' => $transaction && $transaction->paid_at ?
                    Carbon::parse($transaction->paid_at)->format('d M Y') : '-',
                'order_id' => $booking->order_id,
                'days' => $days,
                'months' => $months,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        // Set property_id based on user access
        $propertyId = $user->isSuperAdmin()
            ? $request->input('property_id')
            : $user->property_id;

        $filters = [
            'report_type' => $request->input('report_type', 'checked-in'),
            'selected_date' => $request->input('selected_date'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'property_id' => $propertyId,
            'search' => $request->input('search'),
        ];

        // Dynamic filename based on report type
        $reportLabels = [
            'checked-in' => 'checked-in',
            'waiting-check-in' => 'waiting-check-in',
            'rooms-occupied' => 'rooms-occupied',
            'check-out' => 'check-out',
            'cancelled' => 'cancelled',
        ];

        $reportLabel = $reportLabels[$filters['report_type']] ?? 'booking';
        $filename = 'laporan-booking-' . $reportLabel . '-' . now()->format('Y-m-d-His') . '.xlsx';

        $exporter = new RentedRoomsReportExport($filters);
        return $exporter->export($filename);
    }
}
