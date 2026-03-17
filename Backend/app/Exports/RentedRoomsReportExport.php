<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Property;
use App\Services\ExcelService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RentedRoomsReportExport
{
    protected $filters;
    protected $reportType;

    public function __construct($filters)
    {
        $this->filters = $filters;
        $this->reportType = $filters['report_type'] ?? 'checked-in';
    }

    public function export(string $filename)
    {
        // Get data
        $bookings = $this->getBookings();

        // Calculate totals
        $totalRevenue = $bookings->sum(function ($booking) {
            return $booking->transaction && $booking->transaction->grandtotal_price
                ? $booking->transaction->grandtotal_price
                : 0;
        });

        $dailyCount = $bookings->filter(function ($booking) {
            return $booking->transaction && $booking->transaction->booking_type === 'daily';
        })->count();

        $monthlyCount = $bookings->filter(function ($booking) {
            return $booking->transaction && $booking->transaction->booking_type === 'monthly';
        })->count();

        // Create Excel
        $excel = new ExcelService();
        $sheet = $excel->getActiveSheet();

        // Dynamic title based on report type
        $titles = [
            'checked-in' => '✓ LAPORAN BOOKING - MELAKUKAN CHECK IN',
            'waiting-check-in' => '⏳ LAPORAN BOOKING - MENUNGGU CHECK IN',
            'rooms-occupied' => '🏠 LAPORAN BOOKING - KAMAR TERISI',
            'check-out' => '↗️ LAPORAN BOOKING - MELAKUKAN CHECK OUT',
            'cancelled' => '✕ LAPORAN BOOKING - BOOKING DIBATALKAN',
        ];

        $title = $titles[$this->reportType] ?? '📊 LAPORAN BOOKING';

        // Add Main Title with custom styling
        $excel->addTitleSection($title, [
            'bgColor' => '6366F1', // Indigo
            'textColor' => 'FFFFFF',
            'fontSize' => 20,
            'height' => 40,
            'endColumn' => 'N', // 14 columns
        ]);

        // Add subtitle with generation info
        $excel->addInfoRow('Generated on: ' . now()->format('l, d F Y - H:i:s'), [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'italic' => true,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();

        // Add filter section
        $filterTexts = $this->getFilterTexts();
        $excel->addFilterSection($filterTexts);

        // Add separator
        $excel->addInfoRow('', []);

        // Headers with enhanced styling
        $headers = [
            'No.',
            'Property',
            'Tenant Name',
            'Room Number',
            'Booking Type',
            'Check In',
            'Check Out',
            'Duration',
            'Room Price',
            'Service Fee',
            'Grand Total',
            'Payment Status',
            'Payment Date',
            'Order ID'
        ];

        $excel->addHeader($headers);

        // Style header row with custom colors
        $actualHeaderRow = $excel->getCurrentRow() - 1;
        $sheet->getStyle('A' . $actualHeaderRow . ':N' . $actualHeaderRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6366F1'] // Indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '4F46E5']
                ]
            ]
        ]);
        $sheet->getRowDimension($actualHeaderRow)->setRowHeight(28);

        // Column widths
        $columnWidths = [
            'A' => 8,  // No.
            'B' => 25, // Property
            'C' => 20, // Tenant Name
            'D' => 15, // Room Number
            'E' => 15, // Booking Type
            'F' => 15, // Check In
            'G' => 15, // Check Out
            'H' => 15, // Duration
            'I' => 15, // Room Price
            'J' => 12, // Admin Fee
            'K' => 15, // Grand Total
            'L' => 15, // Payment Status
            'M' => 15, // Payment Date
            'N' => 20, // Order ID
        ];

        foreach ($columnWidths as $col => $width) {
            $excel->setColumnWidth($col, $width);
        }

        // Add data rows with enhanced styling
        $dataStartRow = $excel->getCurrentRow();

        foreach ($bookings as $index => $booking) {
            $row = $this->mapBooking($booking, $index + 1);
            $currentDataRow = $excel->getCurrentRow();

            $excel->addRow($row);

            // Add zebra striping
            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $currentDataRow . ':N' . $currentDataRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }

            // Format as currency for price columns
            $sheet->getStyle('I' . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('K' . $currentDataRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        }

        $dataEndRow = $excel->getCurrentRow() - 1;

        // Style data rows with borders
        if ($bookings->count() > 0) {
            $sheet->getStyle('A' . $dataStartRow . ':N' . $dataEndRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB']
                    ]
                ]
            ]);
        }

        // Add spacing before summary
        $excel->addEmptyRow(2);

        // Add summary section with enhanced design
        $excel->addInfoRow('📊 SUMMARY REPORT', [
            'bold' => true,
            'bgColor' => 'E0E7FF',
            'textColor' => '6366F1',
            'fontSize' => 12,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();

        // Booking breakdown
        $excel->addInfoRow('📈 BOOKING BREAKDOWN', [
            'bold' => true,
            'fontSize' => 11,
            'textColor' => '4B5563',
            'align' => Alignment::HORIZONTAL_LEFT,
            'endColumn' => 'N',
        ]);

        $excel->addInfoRow("Daily Bookings: {$dailyCount} | Monthly Bookings: {$monthlyCount} | Total: {$bookings->count()}", [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'align' => Alignment::HORIZONTAL_RIGHT,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();

        // Total Revenue
        $excel->addSummaryRow('TOTAL REVENUE:', $totalRevenue, [
            'labelColumn' => 'A',
            'valueColumn' => 'K',
            'labelEndColumn' => 'J',
            'bgColor' => 'D1FAE5',
            'textColor' => '059669',
        ]);

        // Format revenue as currency
        $summaryRowNum = $excel->getCurrentRow() - 1;
        $sheet->getStyle('K' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

        // Add footer
        $excel->addEmptyRow();
        $excel->addInfoRow('Report generated by Booking Management System', [
            'fontSize' => 9,
            'italic' => true,
            'textColor' => '9CA3AF',
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        // Freeze panes at header row
        $sheet->freezePane('A' . ($actualHeaderRow + 1));

        // Download
        return $excel->download($filename);
    }

    private function getBookings()
    {
        $query = Booking::with([
                'transaction',
                'payment',
                'property',
                'room'
            ])
            ->orderByDesc('created_at');

        // Apply report type filter (same logic as controller)
        switch ($this->reportType) {
            case 'waiting-check-in':
                // Bookings that are paid but not yet checked in
                // Filter: transaction_status = 'paid' AND check_in_at is NULL
                // Date filter: selected date is between check_in and check_out
                $selectedDate = !empty($this->filters['selected_date'])
                    ? $this->filters['selected_date']
                    : now()->format('Y-m-d');

                $query->whereHas('transaction', function ($q) use ($selectedDate) {
                    $q->where('transaction_status', 'paid')
                      ->whereDate('check_in', '<=', $selectedDate)
                      ->whereDate('check_out', '>=', $selectedDate);
                })->whereNull('check_in_at');
                break;

            case 'checked-in':
                // Bookings that are paid and already checked in
                // Filter: transaction_status = 'paid' AND check_in_at is NOT NULL
                // Date filter: check_in_at date matches selected date
                $selectedDate = !empty($this->filters['selected_date'])
                    ? $this->filters['selected_date']
                    : now()->format('Y-m-d');

                $query->whereNotNull('check_in_at')
                      ->whereDate('check_in_at', $selectedDate)
                      ->whereHas('transaction', function ($q) {
                          $q->where('transaction_status', 'paid');
                      });
                break;

            case 'rooms-occupied':
                // Rooms that are currently occupied
                // Filter: selected date is between booking.check_in_at and transaction.check_out
                $selectedDate = !empty($this->filters['selected_date'])
                    ? $this->filters['selected_date']
                    : now()->format('Y-m-d');

                $query->whereNotNull('check_in_at')
                      ->whereDate('check_in_at', '<=', $selectedDate)
                      ->whereHas('transaction', function ($q) use ($selectedDate) {
                          $q->where('transaction_status', 'paid')
                            ->whereDate('check_out', '>=', $selectedDate);
                      });
                break;

            case 'check-out':
                // Bookings that are checked out
                // Filter: check_in_at NOT NULL AND check_out_at NOT NULL
                // Date filter: check_out_at date matches selected date
                $selectedDate = !empty($this->filters['selected_date'])
                    ? $this->filters['selected_date']
                    : now()->format('Y-m-d');

                $query->whereNotNull('check_in_at')
                      ->whereNotNull('check_out_at')
                      ->whereDate('check_out_at', $selectedDate)
                      ->whereHas('transaction', function ($q) {
                          $q->where('transaction_status', 'paid');
                      });
                break;

            case 'cancelled':
                // Cancelled bookings only
                // Filter: transaction_status = 'cancelled'
                // Date range filter on cancel_at
                $startDate = !empty($this->filters['start_date'])
                    ? $this->filters['start_date']
                    : now()->subMonth()->format('Y-m-d');
                $endDate = !empty($this->filters['end_date'])
                    ? $this->filters['end_date']
                    : now()->format('Y-m-d');

                $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                    $q->where('transaction_status', 'cancelled')
                      ->whereBetween('cancel_at', [
                          $startDate . ' 00:00:00',
                          $endDate . ' 23:59:59'
                      ]);
                });
                break;
        }

        // Property filter
        if (!empty($this->filters['property_id'])) {
            $query->where('property_id', $this->filters['property_id']);
        }

        // Search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get();
    }

    private function mapBooking($booking, $no): array
    {
        $transaction = $booking->transaction;

        // Calculate duration from transaction fields
        $duration = '-';
        if ($transaction) {
            if ($transaction->booking_type === 'daily') {
                $days = $transaction->booking_days ?? 1;
                $duration = $days . ' day' . ($days != 1 ? 's' : '');
            } else {
                $months = $transaction->booking_months ?? 1;
                $duration = $months . ' month' . ($months != 1 ? 's' : '');
            }
        }

        return [
            $no,
            $booking->property ? $booking->property->name : '-',
            $booking->user_name ?? '-',
            $booking->room ? $booking->room->name : '-',
            $transaction ? ucfirst($transaction->booking_type) : '-',
            $transaction && $transaction->check_in ?
                Carbon::parse($transaction->check_in)->format('d M Y') : '-',
            $transaction && $transaction->check_out ?
                Carbon::parse($transaction->check_out)->format('d M Y') : '-',
            $duration,
            $transaction->room_price ?? 0,
            $transaction->service_fee ?? 0,
            $transaction->grandtotal_price ?? 0,
            $transaction ? $transaction->transaction_status : '-',
            $transaction && $transaction->paid_at ?
                Carbon::parse($transaction->paid_at)->format('d M Y') : '-',
            $booking->order_id,
        ];
    }

    private function getFilterTexts(): array
    {
        $filters = [];

        // Report Type
        $reportLabels = [
            'checked-in' => 'Melakukan Check In',
            'waiting-check-in' => 'Menunggu Check In',
            'rooms-occupied' => 'Kamar Terisi',
            'check-out' => 'Melakukan Check Out',
            'cancelled' => 'Booking Dibatalkan',
        ];
        $filters[] = '📊 Report Type: ' . ($reportLabels[$this->reportType] ?? '-');

        // Date filters
        if ($this->reportType === 'cancelled') {
            // Date range for cancelled bookings
            if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
                $filters[] = '📅 Period: ' .
                    Carbon::parse($this->filters['start_date'])->format('d M Y') . ' - ' .
                    Carbon::parse($this->filters['end_date'])->format('d M Y');
            }
        } else {
            // Single date for all other tabs
            if (!empty($this->filters['selected_date'])) {
                $filters[] = '📅 Date: ' .
                    Carbon::parse($this->filters['selected_date'])->format('d M Y');
            }
        }

        // Property
        if (!empty($this->filters['property_id'])) {
            $property = Property::find($this->filters['property_id']);
            if ($property) {
                $filters[] = '🏢 Property: ' . $property->name;
            }
        }

        // Search
        if (!empty($this->filters['search'])) {
            $filters[] = '🔎 Search Keyword: "' . $this->filters['search'] . '"';
        }

        return $filters;
    }
}
