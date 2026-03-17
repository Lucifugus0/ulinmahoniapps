<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Exports\BookingReportExport;
use App\Exports\BookingReportExportNew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel as MaatwebsiteExcel;
use App\Facades\Excel;
use Carbon\Carbon;

class BookingReportController extends Controller
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

        // Get filter values (no default date - show all data)
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $status = $request->input('status');

        // Set property_id based on user access
        if ($user->isSuperAdmin()) {
            $propertyId = $request->input('property_id');
        } else {
            $propertyId = $user->property_id;
        }

        return view('pages.reports.booking-report.index', compact(
            'properties',
            'startDate',
            'endDate',
            'status',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = Booking::with(['room', 'property', 'transaction', 'payment'])
            ->orderByDesc('created_at');

        // Date range filter based on booking date (transaction check_in date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);
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

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('property', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->paginate($request->input('per_page', 15));

        // Transform data for display
        $data = $bookings->map(function ($booking) {
            $transaction = $booking->transaction;
            $payment = $booking->payment;

            // Format payment datetime
            $paymentDatetime = '-';
            if ($payment && $payment->created_at) {
                $paymentDatetime = Carbon::parse($payment->created_at)->format('d M Y H:i');
            } elseif ($transaction && $transaction->paid_at) {
                $paymentDatetime = Carbon::parse($transaction->paid_at)->format('d M Y H:i');
            }

            // Determine payment type
            $paymentType = '-';
            if ($transaction) {
                $paymentType = $transaction->transaction_type ?? '-';
            }

            // Calculate total revenue
            $totalRevenue = 0;
            if ($transaction) {
                $totalRevenue = $transaction->grandtotal_price ?? 0;
            }

            return [
                'booking_date' => $transaction ? Carbon::parse($transaction->check_in)->format('d M Y') : '-',
                'booking_number' => $booking->order_id,
                'name' => $booking->user_name ?? '-',
                'property_name' => $booking->property ? $booking->property->name : '-',
                'address' => $booking->property ? ($booking->property->address ?? '-') : '-',
                'room' => $booking->room ? $booking->room->name : '-',
                'check_in' => $transaction ? Carbon::parse($transaction->check_in)->format('d M Y') : '-',
                'check_out' => $transaction ? Carbon::parse($transaction->check_out)->format('d M Y') : '-',
                'payment_datetime' => $paymentDatetime,
                'payment_type' => $paymentType,
                'total_revenue' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'status' => $booking->status ?? 'Unknown',
                'notes' => $transaction ? $transaction->notes : '',
                'raw_status' => $transaction ? $transaction->transaction_status : 'unknown',
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
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'property_id' => $propertyId,
            'search' => $request->input('search'),
        ];

        $filename = 'booking-report-' . now()->format('Y-m-d-His') . '.xlsx';

        // Try using custom Excel library first (fallback to maatwebsite if needed)
        try {
            $exporter = new BookingReportExportNew($filters);
            return $exporter->export($filename);
        } catch (\Exception $e) {
            // Fallback to maatwebsite/excel if custom library fails
            if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
                return MaatwebsiteExcel::download(new BookingReportExport($filters), $filename);
            }
            throw $e;
        }
    }
}
