<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Property;
use App\Services\ExcelService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BookingReportExportNew
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function export(string $filename)
    {
        // Get data
        $bookings = $this->getBookings();
        $totalRevenue = $bookings->sum(function ($booking) {
            return $booking->transaction && $booking->transaction->grandtotal_price
                ? $booking->transaction->grandtotal_price
                : 0;
        });

        // Create Excel
        $excel = new ExcelService();
        $sheet = $excel->getActiveSheet();

        // Add Main Title with custom styling
        $excel->addTitleSection('📊 LAPORAN BOOKING KAMAR', [
            'bgColor' => '1E3A8A', // Dark blue
            'textColor' => 'FFFFFF',
            'fontSize' => 20,
            'height' => 40,
        ]);

        // Add subtitle with generation info
        $excel->addInfoRow('Generated on: ' . now()->format('l, d F Y - H:i:s'), [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'italic' => true,
            'align' => Alignment::HORIZONTAL_CENTER,
        ]);

        $excel->addEmptyRow();

        // Add filter section
        $filterTexts = $this->getFilterTexts();
        $excel->addFilterSection($filterTexts);

        // Add separator
        $excel->addInfoRow('', []);

        // Remember the header row for freeze pane
        $headerRow = $excel->getCurrentRow();

        // Headers with enhanced styling
        $headers = [
            'Booking Date',
            'Booking Number',
            'Name',
            'Property',
            'Address',
            'Room',
            'Stay Period',
            'Payment',
            'Payment Type',
            'Total Revenue',
            'Status',
            'Notes'
        ];

        $excel->addHeader($headers);

        // Style header row with custom colors
        $actualHeaderRow = $excel->getCurrentRow() - 1;
        $sheet->getStyle('A' . $actualHeaderRow . ':L' . $actualHeaderRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3730A3'] // Indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1E3A8A']
                ]
            ]
        ]);
        $sheet->getRowDimension($actualHeaderRow)->setRowHeight(28);

        // Column widths
        $columnWidths = [
            'A' => 15,  // Booking Date
            'B' => 20,  // Booking Number
            'C' => 20,  // Name
            'D' => 25,  // Property
            'E' => 35,  // Address
            'F' => 20,  // Room
            'G' => 30,  // Stay Period
            'H' => 20,  // Payment
            'I' => 15,  // Payment Type
            'J' => 18,  // Total Revenue
            'K' => 20,  // Status
            'L' => 40,  // Notes
        ];

        foreach ($columnWidths as $col => $width) {
            $excel->setColumnWidth($col, $width);
        }

        // Add data rows with enhanced styling
        $dataStartRow = $excel->getCurrentRow();

        foreach ($bookings as $index => $booking) {
            $row = $this->mapBooking($booking);
            $currentDataRow = $excel->getCurrentRow();

            $excel->addRow($row);

            // Format as currency for revenue column
            $sheet->getStyle('J' . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');

            // Add zebra striping
            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $currentDataRow . ':L' . $currentDataRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
        }

        $dataEndRow = $excel->getCurrentRow() - 1;

        // Style data rows with borders
        if ($bookings->count() > 0) {
            $sheet->getStyle('A' . $dataStartRow . ':L' . $dataEndRow)->applyFromArray([
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
        $excel->addInfoRow('📈 SUMMARY REPORT', [
            'bold' => true,
            'bgColor' => 'E0E7FF',
            'textColor' => '3730A3',
            'fontSize' => 12,
            'align' => Alignment::HORIZONTAL_CENTER,
        ]);

        $excel->addEmptyRow();

        // Total Revenue using new method
        $excel->addSummaryRow('TOTAL REVENUE:', $totalRevenue, [
            'bgColor' => 'D1FAE5',
            'textColor' => '059669',
            'borderColor' => '10B981',
        ]);

        // Format revenue as currency
        $summaryRowNum = $excel->getCurrentRow() - 1;
        $sheet->getStyle('J' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

        // Total Records
        $excel->addInfoRow('Total Records: ' . $bookings->count() . ' bookings', [
            'bold' => true,
            'fontSize' => 10,
            'textColor' => '6B7280',
            'align' => Alignment::HORIZONTAL_RIGHT,
        ]);

        // Add footer
        $excel->addEmptyRow();
        $excel->addInfoRow('Report generated by Booking Management System', [
            'fontSize' => 9,
            'italic' => true,
            'textColor' => '9CA3AF',
            'align' => Alignment::HORIZONTAL_CENTER,
        ]);

        // Freeze panes at header row
        $sheet->freezePane('A' . ($actualHeaderRow + 1));

        // Download
        return $excel->download($filename);
    }

    private function getBookings()
    {
        $query = Booking::with(['room', 'property', 'transaction', 'payment'])
            ->orderByDesc('created_at');

        // Apply filters
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = $this->filters['start_date'];
            $endDate = $this->filters['end_date'];

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);
            });
        }

        if (!empty($this->filters['status'])) {
            switch ($this->filters['status']) {
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

        if (!empty($this->filters['property_id'])) {
            $query->where('property_id', $this->filters['property_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('property', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get();
    }

    private function mapBooking($booking): array
    {
        $transaction = $booking->transaction;
        $payment = $booking->payment;

        // Combine payment date and time
        $paymentDatetime = '-';
        if ($payment && $payment->created_at) {
            $paymentDatetime = Carbon::parse($payment->created_at)->format('d M Y, H:i');
        } elseif ($transaction && $transaction->paid_at) {
            $paymentDatetime = Carbon::parse($transaction->paid_at)->format('d M Y, H:i');
        }

        // Payment type
        $paymentType = '-';
        if ($transaction && $transaction->transaction_type) {
            $paymentType = ucfirst($transaction->transaction_type);
        }

        // Total revenue
        $totalRevenue = 0;
        if ($transaction && $transaction->grandtotal_price) {
            $totalRevenue = $transaction->grandtotal_price;
        }

        // Stay period
        $stayPeriod = '-';
        if ($transaction && $transaction->check_in && $transaction->check_out) {
            $checkIn = Carbon::parse($transaction->check_in)->format('d M Y');
            $checkOut = Carbon::parse($transaction->check_out)->format('d M Y');
            $stayPeriod = $checkIn . ' to ' . $checkOut;
        }

        return [
            $transaction ? Carbon::parse($transaction->check_in)->format('d M Y') : '-',
            $booking->order_id,
            $booking->user_name ?? '-',
            $booking->property ? $booking->property->name : '-',
            $booking->property ? ($booking->property->address ?? '-') : '-',
            $booking->room ? $booking->room->name : '-',
            $stayPeriod,
            $paymentDatetime,
            $paymentType,
            $totalRevenue,
            $booking->status ?? 'Unknown',
            $transaction ? $transaction->notes : '',
        ];
    }

    private function getFilterTexts(): array
    {
        $filters = [];

        // Date Range
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->format('d M Y');
            $endDate = Carbon::parse($this->filters['end_date'])->format('d M Y');
            $filters[] = '📅 Booking Period: ' . $startDate . ' - ' . $endDate;
        }

        // Status
        if (!empty($this->filters['status'])) {
            $statusLabels = [
                'pending' => '⏳ Waiting For Payment',
                'waiting' => '⏰ Waiting Confirmation',
                'waiting-check-in' => '🕐 Waiting For Check-In',
                'checked-in' => '✅ Checked-In',
                'checked-out' => '🏁 Checked-Out',
                'canceled' => '❌ Canceled',
                'expired' => '⌛ Expired'
            ];
            $statusLabel = $statusLabels[$this->filters['status']] ?? $this->filters['status'];
            $filters[] = '📊 Status: ' . $statusLabel;
        }

        // Property/Address
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
