<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataFeed;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Property;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getPropertyRoomReportData($propertyId = null)
    {
        try {
            $user = Auth::user();

            // Filter properties based on user access
            if ($propertyId) {
                $property = Property::findOrFail($propertyId);

                // Check if user has access to this property (enforce property scope)
                if ($user->property_id && $user->property_id != $propertyId) {
                    $property = Property::where('idrec', $user->property_id)->firstOrFail();
                }

                $properties = collect([$property]);
            } else {
                // Get properties based on user role
                $accessiblePropertyId = $user->getAccessiblePropertyId();

                if ($accessiblePropertyId === null) {
                    // Super Admin or HO roles: get all properties
                    $properties = Property::where('status', 1)->get();
                } else {
                    // Site roles: only their assigned property
                    $properties = Property::where('status', 1)
                        ->where('idrec', $accessiblePropertyId)
                        ->get();
                }
            }

            $report = [];

            foreach ($properties as $property) {
                // Total kamar di property ini
                $totalRooms = Room::where('property_id', $property->idrec)
                    ->where('status', 1)
                    ->count();

                // Kamar yang terbooking (semua kamar dengan booking paid yang aktif)
                // Hitung distinct room_id yang memiliki booking aktif (belum check-out)
                // status=1: hanya booking aktif, booking lama dari pindah kamar (status=0) tidak dihitung
                $currentDate = now()->toDateString();

                $bookedRooms = Booking::where('property_id', $property->idrec)
                    ->where('status', 1)
                    ->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })
                    ->whereNull('check_out_at')   // Belum check out
                    ->distinct('room_id')
                    ->count('room_id');

                // Kamar yang sudah check in (terisi secara fisik)
                // Hitung distinct room_id dimana check_in_at != NULL dan check_out_at = NULL
                // status=1: hanya booking aktif, booking lama dari pindah kamar (status=0) tidak dihitung
                $occupiedRooms = Booking::where('property_id', $property->idrec)
                    ->where('status', 1)
                    ->whereHas('transaction', function ($q) {
                        $q->where('transaction_status', 'paid');
                    })
                    ->whereNotNull('check_in_at') // Sudah check-in fisik
                    ->whereNull('check_out_at')   // Belum check out
                    ->distinct('room_id')
                    ->count('room_id');

                // Kamar tersedia (rental_status = 0)
                $availableRooms = Room::where('property_id', $property->idrec)
                    ->where('status', 1)
                    ->where('rental_status', 0)
                    ->count();

                // Data durasi sewa
                $bookingDurations = $this->getBookingDurations($property->idrec);

                // Data penjualan bulan ini
                $monthlySales = $this->getMonthlySales($property->idrec);

                // Breakdown tipe kamar
                $roomTypesBreakdown = $this->getRoomTypesBreakdown($property->idrec);

                $report[$property->idrec] = [
                    'property' => [
                        'id' => $property->idrec,
                        'name' => $property->name ?? 'N/A',
                        'initial' => $property->initial ?? '',
                    ],
                    'room_stats' => [
                        'total_rooms' => $totalRooms,
                        'booked_rooms' => $bookedRooms,
                        'occupied_rooms' => $occupiedRooms,
                        'available_rooms' => $availableRooms,
                        'occupancy_rate' => $totalRooms > 0 ? round(($bookedRooms / $totalRooms) * 100, 2) : 0,
                    ],
                    'booking_durations' => $bookingDurations,
                    'monthly_sales' => $monthlySales,
                    'room_types_breakdown' => $roomTypesBreakdown,
                ];
            }

            return $propertyId ? ($report[$propertyId] ?? []) : $report;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getBookingDurations($propertyId)
    {
        // Durasi statistik dari transactions yang sudah completed (check-out)
        // Use +1 to include both check-in and check-out days (inclusive counting)
        $durations = Transaction::where('property_id', $propertyId)
            ->where('transaction_status', 'paid')
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereHas('booking', function ($q) {
                $q->whereNotNull('check_out_at');
            })
            ->select(
                DB::raw('AVG(DATEDIFF(check_out, check_in) + 1) as avg_duration'),
                DB::raw('MIN(DATEDIFF(check_out, check_in) + 1) as min_duration'),
                DB::raw('MAX(DATEDIFF(check_out, check_in) + 1) as max_duration'),
                DB::raw('COUNT(*) as total_bookings')
            )
            ->first();

        // Breakdown by duration ranges (with inclusive day counting)
        $durationRanges = Transaction::where('property_id', $propertyId)
            ->where('transaction_status', 'paid')
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereHas('booking', function ($q) {
                $q->whereNotNull('check_out_at');
            })
            ->select(
                DB::raw('CASE
                    WHEN DATEDIFF(check_out, check_in) + 1 = 1 THEN "1 Hari"
                    WHEN DATEDIFF(check_out, check_in) + 1 BETWEEN 2 AND 3 THEN "2-3 Hari"
                    WHEN DATEDIFF(check_out, check_in) + 1 BETWEEN 4 AND 7 THEN "4-7 Hari"
                    WHEN DATEDIFF(check_out, check_in) + 1 BETWEEN 8 AND 30 THEN "1-4 Minggu"
                    ELSE "Lebih dari 1 Bulan"
                END as duration_range'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('duration_range')
            ->orderBy(DB::raw('MIN(DATEDIFF(check_out, check_in) + 1)'))
            ->get();

        return [
            'average_duration' => round($durations->avg_duration ?? 0, 1),
            'min_duration' => $durations->min_duration ?? 0,
            'max_duration' => $durations->max_duration ?? 0,
            'total_bookings' => $durations->total_bookings ?? 0,
            'duration_ranges' => $durationRanges
        ];
    }

    private function getMonthlySales($propertyId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $sales = Transaction::where('property_id', $propertyId)
            ->where('transaction_status', 'paid')
            ->whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->select(
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(grandtotal_price) as total_revenue')
            )
            ->first();

        return [
            'total_bookings' => $sales->total_bookings ?? 0,
            'total_revenue' => $sales->total_revenue ?? 0
        ];
    }

    private function getRoomTypesBreakdown($propertyId)
    {
        // Get booked room IDs (transaction paid, belum check-out)
        // status=1: hanya booking aktif, booking lama dari pindah kamar (status=0) tidak dihitung
        $bookedRoomIds = Booking::where('property_id', $propertyId)
            ->where('status', 1)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNull('check_out_at')   // Belum check out
            ->pluck('room_id')
            ->toArray();

        // Get occupied room IDs (sudah check-in fisik)
        // status=1: hanya booking aktif, booking lama dari pindah kamar (status=0) tidak dihitung
        $occupiedRoomIds = Booking::where('property_id', $propertyId)
            ->where('status', 1)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->whereNotNull('check_in_at') // Sudah check-in fisik
            ->whereNull('check_out_at')   // Belum check out
            ->pluck('room_id')
            ->toArray();

        // Get all rooms and group by room name (type)
        $rooms = Room::where('property_id', $propertyId)
            ->where('status', 1)
            ->orderBy('name')
            ->orderBy('no')
            ->get()
            ->groupBy('name')
            ->map(function ($roomGroup, $roomName) use ($bookedRoomIds, $occupiedRoomIds) {
                $roomDetails = $roomGroup->map(function ($room) use ($bookedRoomIds, $occupiedRoomIds) {
                    $isBooked = in_array($room->idrec, $bookedRoomIds);
                    $isOccupied = in_array($room->idrec, $occupiedRoomIds);

                    // Determine status
                    $status = 'available';
                    if ($isOccupied) {
                        $status = 'occupied';
                    } elseif ($isBooked) {
                        $status = 'booked';
                    } else {
                        $status = $room->rental_status == 0 ? 'available' : 'unavailable';
                    }

                    return [
                        'room_number' => $room->no ?? '-',
                        'status' => $status,
                    ];
                });

                // Count status
                $statusCounts = [
                    'available' => $roomDetails->where('status', 'available')->count(),
                    'booked' => $roomDetails->where('status', 'booked')->count(),
                    'occupied' => $roomDetails->where('status', 'occupied')->count(),
                    'unavailable' => $roomDetails->where('status', 'unavailable')->count(),
                ];

                return [
                    'name' => $roomName,
                    'total_rooms' => $roomGroup->count(),
                    'rooms' => $roomDetails->toArray(),
                    'status_counts' => $statusCounts,
                ];
            })
            ->values();

        return $rooms;
    }

    private function getCurrentOccupancyData($propertyId = null)
    {
        $query = Transaction::where('transaction_status', 'paid')
            ->whereDate('check_in', '<=', now()->toDateString())
            ->whereDate('check_out', '>=', now()->toDateString())
            ->whereHas('booking', function ($q) {
                $q->whereNull('check_out_at');
            });

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        return [
            'current_guests' => $query->count(),
            'total_revenue_today' => Transaction::where('transaction_status', 'paid')
                ->whereDate('paid_at', now()->toDateString())
                ->when($propertyId, function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                })
                ->sum('grandtotal_price')
        ];
    }

    private function getOccupiedRoomsDetails($propertyId = null)
    {
        return Booking::with(['room', 'property', 'transaction', 'user'])
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid')
                    ->whereDate('check_in', '<=', now()->toDateString())
                    ->whereDate('check_out', '>=', now()->toDateString());
            })
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->get()
            ->map(function ($booking) {
                $checkIn = Carbon::parse($booking->transaction->check_in ?? now())->startOfDay();
                $checkOut = Carbon::parse($booking->transaction->check_out ?? now())->startOfDay();
                $today = Carbon::now()->startOfDay();

                // Calculate days correctly (inclusive)
                $daysStayed = $checkIn->diffInDays($today) + 1; // +1 to include check-in day
                $totalDays = $checkIn->diffInDays($checkOut) + 1; // +1 to include both check-in and check-out days
                $daysRemaining = max(0, $today->diffInDays($checkOut)); // Ensure non-negative

                // Adjust for overdue
                if ($checkOut->isPast() && !$checkOut->isToday()) {
                    $daysRemaining = 0;
                    $daysStayed = $totalDays + $checkOut->diffInDays($today);
                }

                return [
                    'booking_id' => $booking->idrec,
                    'guest_name' => $booking->user_name ?? $booking->transaction->user_name ?? 'N/A',
                    'room_name' => $booking->room->name ?? 'N/A',
                    'room_number' => $booking->room->no ?? 'N/A',
                    'property_name' => $booking->property->name ?? 'N/A',
                    'check_in_date' => $checkIn->format('d M Y'),
                    'check_out_date' => $checkOut->format('d M Y'),
                    'days_stayed' => $daysStayed,
                    'total_days' => $totalDays,
                    'days_remaining' => $daysRemaining,
                    'progress_percentage' => $totalDays > 0 ? min(100, round(($daysStayed / $totalDays) * 100)) : 0,
                    'total_price' => $booking->transaction->grandtotal_price ?? 0,
                    'daily_rate' => $totalDays > 0 ? round(($booking->transaction->grandtotal_price ?? 0) / $totalDays, 2) : 0,
                    'is_checkout_today' => $checkOut->isToday(),
                    'is_overdue' => $checkOut->isPast() && !$checkOut->isToday(),
                ];
            });
    }

    private function getOccupancyHistory($days = 30, $propertyId = null)
    {
        $history = [];
        $totalRooms = Room::where('status', 1)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->count();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();

            // Count rooms occupied on this specific date
            // Only count rooms where guest has actually checked in
            $occupied = Booking::when($propertyId, function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                })
                ->whereHas('transaction', function ($q) use ($date) {
                    $q->where('transaction_status', 'paid')
                        ->whereDate('check_in', '<=', $date)
                        ->whereDate('check_out', '>=', $date);
                })
                ->whereNotNull('check_in_at') // Must be physically checked in
                ->where(function ($q) use ($date) {
                    // And either not checked out yet, or checked out after this date
                    $q->whereNull('check_out_at')
                        ->orWhereDate('check_out_at', '>', $date);
                })
                ->count();

            $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 2) : 0;

            $history[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'occupied' => $occupied,
                'occupancy_rate' => $occupancyRate,
            ];
        }

        return $history;
    }

    private function getRentalDurationTrends($propertyId = null)
    {
        // Compare current month with previous month
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $previousDate = $now->copy()->subMonth();
        $previousMonth = $previousDate->month;
        $previousYear = $previousDate->year;

        // Current month data - only completed or ongoing bookings
        $currentMonthData = Transaction::where('transaction_status', 'paid')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->whereYear('check_in', $currentYear)
            ->whereMonth('check_in', $currentMonth)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->select(
                DB::raw('AVG(DATEDIFF(check_out, check_in) + 1) as avg_duration'), // +1 for inclusive count
                DB::raw('COUNT(*) as total_bookings')
            )
            ->first();

        // Previous month data
        $previousMonthData = Transaction::where('transaction_status', 'paid')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->whereYear('check_in', $previousYear)
            ->whereMonth('check_in', $previousMonth)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->select(
                DB::raw('AVG(DATEDIFF(check_out, check_in) + 1) as avg_duration'), // +1 for inclusive count
                DB::raw('COUNT(*) as total_bookings')
            )
            ->first();

        $currentAvg = round($currentMonthData->avg_duration ?? 0, 1);
        $previousAvg = round($previousMonthData->avg_duration ?? 0, 1);

        // Calculate trend
        if ($previousAvg > 0) {
            $trend = round((($currentAvg - $previousAvg) / $previousAvg) * 100, 1);
        } elseif ($currentAvg > 0) {
            $trend = 100; // 100% increase from 0
        } else {
            $trend = 0; // Both are 0
        }

        return [
            'current_month_avg' => $currentAvg,
            'previous_month_avg' => $previousAvg,
            'trend_percentage' => abs($trend),
            'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
            'current_bookings' => $currentMonthData->total_bookings ?? 0,
            'previous_bookings' => $previousMonthData->total_bookings ?? 0,
        ];
    }

    private function getRevenuePerOccupiedRoom($propertyId = null)
    {
        $today = now()->toDateString();

        // Get bookings that are currently occupied (checked in but not checked out)
        $occupiedBookings = Booking::with('transaction')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->whereHas('transaction', function ($q) use ($today) {
                $q->where('transaction_status', 'paid')
                    ->whereDate('check_in', '<=', $today)
                    ->whereDate('check_out', '>=', $today);
            })
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->get();

        $occupiedRooms = $occupiedBookings->count();
        $totalRevenue = $occupiedBookings->sum(function ($booking) {
            return $booking->transaction->grandtotal_price ?? 0;
        });

        $averageRevenuePerRoom = $occupiedRooms > 0
            ? round($totalRevenue / $occupiedRooms, 2)
            : 0;

        return [
            'occupied_rooms' => $occupiedRooms,
            'total_revenue' => $totalRevenue,
            'average_per_room' => $averageRevenuePerRoom,
        ];
    }

    // Finance Statistics Methods
    private function getTodayRevenue($propertyId = null)
    {
        $today = now()->toDateString();

        $data = Transaction::where('transaction_status', 'paid')
            ->whereDate('paid_at', $today)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->select(
                DB::raw('SUM(grandtotal_price) as total_revenue'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->first();

        return [
            'revenue' => $data->total_revenue ?? 0,
            'transactions' => $data->total_transactions ?? 0
        ];
    }

    private function getMonthlyRevenue($propertyId = null)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $data = Transaction::where('transaction_status', 'paid')
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->select(
                DB::raw('SUM(grandtotal_price) as total_revenue'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->first();

        // Hitung pendapatan bulan lalu untuk perbandingan
        $lastMonth = now()->subMonth();
        $lastMonthData = Transaction::where('transaction_status', 'paid')
            ->whereYear('paid_at', $lastMonth->year)
            ->whereMonth('paid_at', $lastMonth->month)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->select(
                DB::raw('SUM(grandtotal_price) as total_revenue')
            )
            ->first();

        $currentRevenue = $data->total_revenue ?? 0;
        $lastMonthRevenue = $lastMonthData->total_revenue ?? 0;

        // Hitung persentase perubahan
        $revenueChange = 0;
        if ($lastMonthRevenue > 0) {
            $revenueChange = round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } elseif ($currentRevenue > 0) {
            $revenueChange = 100;
        }

        return [
            'revenue' => $currentRevenue,
            'transactions' => $data->total_transactions ?? 0,
            'target' => 150000000, // Target bisa disesuaikan atau diambil dari setting
            'percentage' => $currentRevenue > 0
                ? round(($currentRevenue / 150000000) * 100, 1)
                : 0,
            'last_month_revenue' => $lastMonthRevenue,
            'revenue_change' => $revenueChange,
        ];
    }

    private function getPendingPayments($propertyId = null)
    {
        $data = Transaction::whereIn('transaction_status', ['pending', 'waiting'])
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->select(
                DB::raw('SUM(grandtotal_price) as total_pending'),
                DB::raw('COUNT(*) as total_invoices')
            )
            ->first();

        return [
            'amount' => $data->total_pending ?? 0,
            'count' => $data->total_invoices ?? 0
        ];
    }

    private function getPaymentSuccessRate($propertyId = null)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalTransactions = Transaction::whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->count();

        $paidTransactions = Transaction::where('transaction_status', 'paid')
            ->whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->count();

        $successRate = $totalTransactions > 0
            ? round(($paidTransactions / $totalTransactions) * 100, 1)
            : 0;

        return $successRate;
    }

    private function getPaymentMethodBreakdown($propertyId = null, $period = 'monthly')
    {
        // Get payment data grouped by payment method
        $query = Payment::join('t_transactions', 't_payment.order_id', '=', 't_transactions.order_id')
            ->where('t_transactions.transaction_status', 'paid')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('t_transactions.property_id', $propertyId);
            });

        if ($period === 'daily') {
            $query->whereDate('t_transactions.paid_at', now()->toDateString());
        } elseif ($period === 'yearly') {
            $query->whereYear('t_transactions.paid_at', now()->year);
        } elseif ($period === 'all') {
            // no date filter
        } else {
            $query->whereYear('t_transactions.paid_at', now()->year)
                  ->whereMonth('t_transactions.paid_at', now()->month);
        }

        $paymentData = $query
            ->select(
                't_payment.payment_status as method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(t_payment.grandtotal_price) as total')
            )
            ->groupBy('t_payment.payment_status')
            ->get();

        // Calculate total for percentage
        $grandTotal = $paymentData->sum('total');

        // Map payment methods to friendly names
        $methodMapping = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'e-wallet' => 'E-Wallet',
            'debit_card' => 'Kartu Debit',
        ];

        $breakdown = [];
        foreach ($paymentData as $payment) {
            $methodName = $methodMapping[$payment->method] ?? ucfirst(str_replace('_', ' ', $payment->method));
            $percentage = $grandTotal > 0 ? round(($payment->total / $grandTotal) * 100) : 0;

            $breakdown[] = [
                'method' => $methodName,
                'count' => $payment->count,
                'amount' => $payment->total,
                'percentage' => $percentage
            ];
        }

        // Sort by amount descending
        usort($breakdown, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        return [
            'breakdown' => $breakdown,
            'total' => $grandTotal
        ];
    }

    private function getCashFlowSummary($propertyId = null, $days = 7)
    {
        // Get last X days cash flow
        $dailyCashFlow = [];
        $totalCashIn = 0;
        $totalCashOut = 0;

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();

            // Cash In - Paid transactions (handle null paid_at)
            $cashIn = Transaction::where('transaction_status', 'paid')
                ->whereNotNull('paid_at')
                ->whereDate('paid_at', $date->toDateString())
                ->when($propertyId, function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                })
                ->sum('grandtotal_price') ?? 0;

            // Cash Out - Refunds (if you have refund table)
            $cashOut = 0;
            // If you have refund table, uncomment and adjust:
            // $cashOut = Refund::whereDate('refund_date', $date)
            //     ->when($propertyId, fn($q) => $q->where('property_id', $propertyId))
            //     ->sum('amount') ?? 0;

            $netFlow = $cashIn - $cashOut;
            $totalCashIn += $cashIn;
            $totalCashOut += $cashOut;

            $dailyCashFlow[] = [
                'date' => $date->format('d M'),
                'date_full' => $date->format('Y-m-d'),
                'day_name' => $date->format('D'),
                'cash_in' => (float) $cashIn,
                'cash_out' => (float) $cashOut,
                'net_flow' => (float) $netFlow,
                'is_today' => $date->isToday(),
            ];
        }

        // Recent transactions (last 8 paid transactions with eager loading)
        $recentTransactions = Transaction::with(['booking.room', 'user', 'property', 'payment'])
            ->where('transaction_status', 'paid')
            ->whereNotNull('paid_at')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->orderBy('paid_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($transaction) {
                $paidAt = $transaction->paid_at ? Carbon::parse($transaction->paid_at) : now();

                // Map payment method to friendly names
                $methodMapping = [
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer',
                    'credit_card' => 'Kartu Kredit',
                    'e-wallet' => 'E-Wallet',
                    'debit_card' => 'Debit',
                ];

                $rawMethod = $transaction->payment->payment_status ?? 'N/A';
                $paymentMethod = $methodMapping[$rawMethod] ?? ucfirst(str_replace('_', ' ', $rawMethod));

                return [
                    'transaction_code' => $transaction->transaction_code ?? 'N/A',
                    'guest_name' => $transaction->user_name ?? 'Guest',
                    'room_name' => $transaction->room_name ?? 'N/A',
                    'property_name' => $transaction->property->name ?? 'N/A',
                    'paid_date' => $paidAt->format('d M Y'),
                    'paid_time' => $paidAt->format('H:i'),
                    'amount' => (float) ($transaction->grandtotal_price ?? 0),
                    'payment_method' => $paymentMethod,
                    'is_today' => $paidAt->isToday(),
                ];
            });

        // Calculate average daily revenue
        $daysCount = count($dailyCashFlow);
        $avgDailyRevenue = $daysCount > 0 ? round($totalCashIn / $daysCount, 2) : 0;

        // Calculate trend - dynamically adjust based on period length
        $cashFlowCollection = collect($dailyCashFlow);
        $recentDaysCount = $days <= 7 ? 3 : 7; // Last 3 days for 7-day period, last 7 days for 30-day period
        $recentDays = $cashFlowCollection->slice(-$recentDaysCount);
        $previousDays = $cashFlowCollection->slice(0, $days - $recentDaysCount);

        $recentTotal = $recentDays->sum('cash_in');
        $previousTotal = $previousDays->sum('cash_in');

        // Calculate average for fair comparison
        $recentAvg = $recentDays->count() > 0 ? $recentTotal / $recentDays->count() : 0;
        $previousAvg = $previousDays->count() > 0 ? $previousTotal / $previousDays->count() : 0;

        $trend = 'stable';
        $trendPercentage = 0;

        if ($previousAvg > 0) {
            $trendPercentage = round((($recentAvg - $previousAvg) / $previousAvg) * 100, 1);
            if ($trendPercentage > 5) {
                $trend = 'up';
            } elseif ($trendPercentage < -5) {
                $trend = 'down';
            }
        } elseif ($recentAvg > 0) {
            // If previous avg is 0 but recent has value, it's trending up
            $trend = 'up';
            $trendPercentage = 100;
        }

        // Calculate peak day
        $peakDay = $cashFlowCollection->sortByDesc('cash_in')->first();
        $peakDayInfo = $peakDay ? [
            'day' => $peakDay['day_name'],
            'amount' => $peakDay['cash_in'],
            'date' => $peakDay['date']
        ] : null;

        return [
            'daily_cash_flow' => $dailyCashFlow,
            'total_cash_in' => (float) $totalCashIn,
            'total_cash_out' => (float) $totalCashOut,
            'net_cash_flow' => (float) ($totalCashIn - $totalCashOut),
            'recent_transactions' => $recentTransactions,
            'avg_daily_revenue' => (float) $avgDailyRevenue,
            'trend' => $trend,
            'trend_percentage' => abs($trendPercentage),
            'peak_day' => $peakDayInfo,
            'total_transactions_count' => $recentTransactions->count(),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        // Use the new helper method to get accessible property ID
        $userPropertyId = $user->getAccessiblePropertyId();

        // Check if user is HO or Site
        $isHO = $user->isHO();
        $isSite = $user->isSite();

        // Get current occupancy for Site users
        $currentOccupancy = 0;
        $propertyName = null;
        if ($isSite && $userPropertyId) {
            $currentOccupancy = Booking::where('property_id', $userPropertyId)
                ->whereHas('transaction', fn($q) => $q->where('transaction_status', 'paid'))
                ->whereNotNull('check_in_at')
                ->whereNull('check_out_at')
                ->count();
            $propertyName = $user->property->name ?? null;
        }

        $dataFeed = new DataFeed();

        // Get today's check-ins (guests with scheduled check-in date = today)
        $checkIns = Booking::with(['user', 'room', 'property', 'transaction'])
            ->when($userPropertyId, function ($q) use ($userPropertyId) {
                $q->where('property_id', $userPropertyId);
            })
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid')
                    ->whereDate('check_in', now()->toDateString());
            })
            ->whereNull('check_out_at')
            ->orderBy('check_in_at', 'desc')
            ->limit(4)
            ->get();

        // Get upcoming check-outs (paid bookings with check-out date within 3 days, checked in but not checked out)
        $checkOuts = Booking::with(['user', 'room', 'property', 'transaction'])
            ->when($userPropertyId, function ($q) use ($userPropertyId) {
                $q->where('property_id', $userPropertyId);
            })
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid')
                    ->whereDate('check_out', '>=', now()->toDateString())
                    ->whereDate('check_out', '<=', now()->addDays(3)->toDateString());
            })
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->orderBy(
                Transaction::select('check_out')
                    ->whereColumn('t_transactions.order_id', 't_booking.order_id')
                    ->limit(1)
            )
            ->limit(4)
            ->get();

        $stats = [
            'upcoming' => Booking::when($userPropertyId, function ($q) use ($userPropertyId) {
                    $q->where('property_id', $userPropertyId);
                })
                ->whereHas('transaction', fn($q) =>
                    $q->where('transaction_status', 'paid')
                        ->whereDate('check_in', '>=', now()))
                ->whereNull('check_in_at')
                ->whereNull('check_out_at')
                ->count(),

            'today' => Booking::when($userPropertyId, function ($q) use ($userPropertyId) {
                    $q->where('property_id', $userPropertyId);
                })
                ->whereHas('transaction', fn($q) => $q->where('transaction_status', 'paid'))
                ->whereNull('check_in_at')
                ->whereNull('check_out_at')
                ->whereHas('transaction', fn($q) => $q->whereDate('check_in', now()->toDateString()))
                ->count(),

            'checkin' => Booking::when($userPropertyId, function ($q) use ($userPropertyId) {
                    $q->where('property_id', $userPropertyId);
                })
                ->whereHas('transaction', fn($q) => $q->where('transaction_status', 'paid'))
                ->whereNotNull('check_in_at')
                ->whereNull('check_out_at')
                ->count(),

            'checkout' => Booking::when($userPropertyId, function ($q) use ($userPropertyId) {
                    $q->where('property_id', $userPropertyId);
                })
                ->whereHas('transaction', fn($q) => $q->where('transaction_status', 'paid'))
                ->whereNotNull('check_in_at')
                ->whereNull('check_out_at')
                ->whereHas('transaction', fn($q) =>
                    $q->whereDate('check_out', '>=', now()->toDateString())
                      ->whereDate('check_out', '<=', now()->addDays(3)->toDateString())
                )
                ->count(),
        ];

        // Sales Report Data (Last 30 days)
        $salesReport = $this->getSalesReport($userPropertyId);

        // Get room availability for the next 7 days
        $startDate = now();
        $endDate = now()->addDays(7);

        // Get all room types with their total count
        $roomTypes = Room::select('type', DB::raw('count(*) as total'))
            ->when($userPropertyId, function ($q) use ($userPropertyId) {
                $q->where('property_id', $userPropertyId);
            })
            ->groupBy('type')
            ->get();

        // Get unavailable rooms (booked and not checked out yet)
        $unavailableRooms = Booking::where(function ($query) use ($startDate, $endDate) {
            $query->where('check_in_at', '<=', $endDate)
                ->where('check_out_at', '>=', $startDate)
                ->orWhere(function ($query) use ($endDate) {
                    $query->where('check_in_at', '<=', $endDate)
                        ->whereNull('check_out_at');
                });
        })
            ->when($userPropertyId, function ($q) use ($userPropertyId) {
                $q->where('t_booking.property_id', $userPropertyId);
            })
            ->select('room_id', 'type')
            ->join('m_rooms', 't_booking.room_id', '=', 'm_rooms.idrec')
            ->groupBy('room_id', 'type')
            ->get()
            ->groupBy('type');

        // Calculate availability for each room type
        $roomAvailability = [];
        foreach ($roomTypes as $roomType) {
            $unavailableCount = isset($unavailableRooms[$roomType->type]) ?
                count($unavailableRooms[$roomType->type]) : 0;

            $available = $roomType->total - $unavailableCount;
            $percentage = $roomType->total > 0 ? round(($available / $roomType->total) * 100) : 0;

            $roomAvailability[] = [
                'type' => $roomType->type,
                'total' => $roomType->total,
                'available' => $available,
                'percentage' => $percentage,
                'is_popular' => $roomType->type === 'Deluxe Suite',
                'is_luxury' => $roomType->type === 'Presidential Suite'
            ];
        }

        // Get room reports - Let the method handle property filtering internally
        // so it always returns the keyed-by-property-ID format the blade expects
        $roomReports = $this->getPropertyRoomReportData();

        // Get new dashboard data
        $occupiedRooms = $this->getOccupiedRoomsDetails($userPropertyId);
        $occupancyHistory = $this->getOccupancyHistory(30, $userPropertyId);
        $rentalDurationTrends = $this->getRentalDurationTrends($userPropertyId);
        $revenuePerRoom = $this->getRevenuePerOccupiedRoom($userPropertyId);

        // Get finance data - check if user has any finance widget access via role_dashboard_widgets
        $financeStats = [];
        $hasFinanceAccess = $user->role && (
            $user->role->hasWidgetAccess('finance_today_revenue') ||
            $user->role->hasWidgetAccess('finance_monthly_revenue') ||
            $user->role->hasWidgetAccess('finance_pending_payments') ||
            $user->role->hasWidgetAccess('finance_payment_success_rate') ||
            $user->role->hasWidgetAccess('finance_payment_methods')
        );

        if ($hasFinanceAccess) {
            $todayRevenue = $this->getTodayRevenue($userPropertyId);
            $monthlyRevenue = $this->getMonthlyRevenue($userPropertyId);
            $pendingPayments = $this->getPendingPayments($userPropertyId);
            $paymentSuccessRate = $this->getPaymentSuccessRate($userPropertyId);
            $paymentMethodBreakdown = $this->getPaymentMethodBreakdown($userPropertyId);
            $cashFlowSummary = $this->getCashFlowSummary($userPropertyId);

            $financeStats = [
                'today_revenue' => $todayRevenue['revenue'] ?? 0,
                'today_transactions' => $todayRevenue['transactions'] ?? 0,
                'monthly_revenue' => $monthlyRevenue['revenue'] ?? 0,
                'monthly_transactions' => $monthlyRevenue['transactions'] ?? 0,
                'monthly_target' => $monthlyRevenue['target'] ?? 150000000,
                'monthly_percentage' => $monthlyRevenue['percentage'] ?? 0,
                'last_month_revenue' => $monthlyRevenue['last_month_revenue'] ?? 0,
                'revenue_change' => $monthlyRevenue['revenue_change'] ?? 0,
                'pending_payments' => $pendingPayments['amount'] ?? 0,
                'pending_count' => $pendingPayments['count'] ?? 0,
                'payment_success_rate' => $paymentSuccessRate ?? 0,
                'payment_methods' => $paymentMethodBreakdown['breakdown'] ?? [],
                'payment_methods_total' => $paymentMethodBreakdown['total'] ?? 0,
                'cash_flow' => $cashFlowSummary['daily_cash_flow'] ?? [],
                'total_cash_in' => $cashFlowSummary['total_cash_in'] ?? 0,
                'total_cash_out' => $cashFlowSummary['total_cash_out'] ?? 0,
                'net_cash_flow' => $cashFlowSummary['net_cash_flow'] ?? 0,
                'recent_transactions' => $cashFlowSummary['recent_transactions'] ?? [],
                'avg_daily_revenue' => $cashFlowSummary['avg_daily_revenue'] ?? 0,
                'cash_flow_trend' => $cashFlowSummary['trend'] ?? 'stable',
                'cash_flow_trend_percentage' => $cashFlowSummary['trend_percentage'] ?? 0,
            ];
        }

        $showActions = true;

        return view('pages/dashboard/dashboard', compact(
            'dataFeed',
            'checkIns',
            'checkOuts',
            'roomAvailability',
            'stats',
            'salesReport',
            'showActions',
            'roomReports',
            'occupiedRooms',
            'occupancyHistory',
            'rentalDurationTrends',
            'revenuePerRoom',
            'financeStats',
            'isHO',
            'isSite',
            'currentOccupancy',
            'propertyName'
        ));
    }

    private function getSalesReport($propertyId = null)
    {
        $startDate = now()->subDays(30);
        $endDate = now();

        $salesData = Transaction::where('transaction_status', 'paid')
            ->when($propertyId, function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(grandtotal_price) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $salesData->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            }),
            'bookings' => $salesData->pluck('total_bookings'),
            'revenue' => $salesData->pluck('total_revenue')
        ];
    }

    public function getPropertyRoomReport($propertyId = null)
    {
        $user = Auth::user();
        // Check if user has widget permission to view rooms availability via role_dashboard_widgets
        if (!$user->role || !$user->role->hasWidgetAccess('rooms_availability')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No access to room availability widget'
            ], 403);
        }

        // Site users: enforce their property scope
        if ($user->isSite() && $user->property_id) {
            $propertyId = $user->property_id;
        }

        $data = $this->getPropertyRoomReportData($propertyId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Room report retrieved successfully'
        ]);
    }

    public function getPaymentMethods(Request $request)
    {
        $user = Auth::user();

        if (!$user->role || !$user->role->hasWidgetAccess('finance_payment_methods')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $propertyId = ($user->isSite() && $user->property_id) ? $user->property_id : null;
        $period = $request->input('period', 'all');
        $breakdown = $this->getPaymentMethodBreakdown($propertyId, $period);

        return response()->json(['success' => true, 'data' => $breakdown]);
    }

    public function getPropertyRevenue(Request $request, $propertyId = null)
    {
        $user = Auth::user();

        // Check if user has widget permission via role_dashboard_widgets
        if (!$user->role || !$user->role->hasWidgetAccess('rooms_property_report')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No access to property revenue widget'
            ], 403);
        }

        // Site users: enforce their property scope
        if ($user->isSite() && $user->property_id) {
            $propertyId = $user->property_id;
        }

        $period = $request->input('period', 'all');

        try {
            if ($propertyId) {
                // Get single property revenue
                $property = Property::findOrFail($propertyId);
                $data = $this->getPropertyRevenueData($propertyId, $property, $period);
            } else {
                // Get all properties revenue (HO users only)
                $properties = Property::where('status', 1)->get();
                $data = [];

                foreach ($properties as $property) {
                    $data[] = $this->getPropertyRevenueData($property->idrec, $property, $period);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching revenue data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPropertyRevenueData($propertyId, $property, $period = 'monthly')
    {
        $now = now();
        $base = Transaction::where('property_id', $propertyId)->where('transaction_status', 'paid');

        if ($period === 'daily') {
            $revenue  = (clone $base)->whereDate('paid_at', $now->toDateString())->sum('grandtotal_price') ?? 0;
            $bookings = (clone $base)->whereDate('paid_at', $now->toDateString())->count();
        } elseif ($period === 'yearly') {
            $revenue  = (clone $base)->whereYear('paid_at', $now->year)->sum('grandtotal_price') ?? 0;
            $bookings = (clone $base)->whereYear('paid_at', $now->year)->count();
        } elseif ($period === 'all') {
            $revenue  = (clone $base)->sum('grandtotal_price') ?? 0;
            $bookings = (clone $base)->count();
        } else {
            $revenue  = (clone $base)->whereYear('paid_at', $now->year)->whereMonth('paid_at', $now->month)->sum('grandtotal_price') ?? 0;
            $bookings = (clone $base)->whereYear('paid_at', $now->year)->whereMonth('paid_at', $now->month)->count();
        }

        return [
            'property_id'   => $propertyId,
            'property_name' => $property->name ?? 'N/A',
            'revenue'       => $revenue,
            'total_bookings' => $bookings,
        ];
    }

    public function getRevenueTrend(Request $request, $propertyId = null)
    {
        $user = Auth::user();

        // Check if user has widget permission via role_dashboard_widgets
        if (!$user->role || !$user->role->hasWidgetAccess('report_sales_chart')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No access to revenue trend widget'
            ], 403);
        }

        // Site users: enforce their property scope
        if ($user->isSite() && $user->property_id) {
            $propertyId = $user->property_id;
        }

        // Get days parameter from request (default to 7)
        $days = $request->input('days', 7);

        // Validate days parameter (only allow 7 or 30)
        if (!in_array($days, [7, 30])) {
            $days = 7;
        }

        try {
            $cashFlowSummary = $this->getCashFlowSummary($propertyId, $days);

            return response()->json([
                'success' => true,
                'data' => $cashFlowSummary['daily_cash_flow'],
                'days' => $days
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching revenue trend: ' . $e->getMessage()
            ], 500);
        }
    }

    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }

    public function progress_index()
    {
        return view('pages/progress_page/index');
    }
}