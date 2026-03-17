<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Property;
use App\Services\ExcelService;
use App\Services\InvoiceNumberService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PaymentReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function export(string $filename)
    {
        // Get data
        $payments = $this->getPayments();

        // Generate invoice numbers batch
        $invoiceNumbers = InvoiceNumberService::generateBatch($payments);

        $totalRevenue = $payments->sum(function ($transaction) {
            return $transaction->grandtotal_price ?? 0;
        });
        $totalRefunds = $payments->filter(function ($transaction) {
            return $transaction->booking && $transaction->booking->refund;
        })->count();

        // Create Excel
        $excel = new ExcelService();
        $sheet = $excel->getActiveSheet();

        // Add Main Title with custom styling
        $excel->addTitleSection('LAPORAN KEUANGAN PEMBAYARAN', [
            'bgColor' => '059669', // Green
            'textColor' => 'FFFFFF',
            'fontSize' => 20,
            'height' => 40,
            'endColumn' => 'AD', // 30 columns
        ]);

        // Add subtitle with generation info
        $excel->addInfoRow('Generated on: ' . now()->format('l, d F Y - H:i:s'), [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'italic' => true,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'AD',
        ]);

        $excel->addEmptyRow();

        // Add filter section
        $filterTexts = $this->getFilterTexts();
        $excel->addFilterSection($filterTexts);

        // Add separator
        $excel->addInfoRow('', []);

        // Headers with enhanced styling (30 columns)
        $headers = [
            'No',
            'Invoice Number',
            'Invoice Date',
            'Transaction Code',
            'Property Name',
            'Room Type',
            'Room Number',
            'Tenant Name',
            'NIK',
            'Mobile Number',
            'Email',
            'Check In',
            'Check Out',
            'Duration',
            'Price Kamar Per Unit',
            'DPP Kamar Per Unit',
            'Subtotal',
            'Diskon',
            'DPP Diskon',
            'Parkir',
            'DPP Parkir',
            'VATT 11%',
            'Grand Total',
            'Deposit',
            'Service Fee',
            'Payment Status',
            'Status Sewa',
            'Verified By',
            'Verified Date',
            'Notes',
        ];

        $excel->addHeader($headers);

        // Style header row with custom colors
        $actualHeaderRow = $excel->getCurrentRow() - 1;
        $sheet->getStyle('A' . $actualHeaderRow . ':AD' . $actualHeaderRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'] // Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '047857']
                ]
            ]
        ]);
        $sheet->getRowDimension($actualHeaderRow)->setRowHeight(35);

        // Column widths (29 columns: A-AC)
        $columnWidths = [
            'A' => 6,   // No
            'B' => 28,  // Invoice Number
            'C' => 18,  // Invoice Date
            'D' => 20,  // Transaction Code
            'E' => 22,  // Property Name
            'F' => 15,  // Room Type
            'G' => 12,  // Room Number
            'H' => 20,  // Tenant Name
            'I' => 18,  // NIK
            'J' => 16,  // Mobile Number
            'K' => 25,  // Email
            'L' => 14,  // Check In
            'M' => 14,  // Check Out
            'N' => 10,  // Duration
            'O' => 18,  // Price Kamar Per Unit
            'P' => 18,  // DPP Kamar Per Unit
            'Q' => 18,  // Subtotal
            'R' => 15,  // Diskon
            'S' => 15,  // DPP Diskon
            'T' => 15,  // Parkir
            'U' => 15,  // DPP Parkir
            'V' => 15,  // VATT 11%
            'W' => 18,  // Grand Total
            'X' => 15,  // Deposit
            'Y' => 15,  // Service Fee
            'Z' => 14,  // Payment Status
            'AA' => 18, // Verified By
            'AB' => 18, // Verified Date
            'AC' => 35, // Notes
            'AD' => 16, // Status Sewa
        ];

        foreach ($columnWidths as $col => $width) {
            $excel->setColumnWidth($col, $width);
        }

        // Add data rows with enhanced styling
        $dataStartRow = $excel->getCurrentRow();

        foreach ($payments as $index => $transaction) {
            $invoiceNumber = $invoiceNumbers[$transaction->idrec] ?? '-';
            $row = $this->mapPayment($transaction, $index + 1, $invoiceNumber);
            $currentDataRow = $excel->getCurrentRow();

            $excel->addRow($row);

            // Force NIK and Mobile Number as text to prevent scientific notation / formula parsing
            $sheet->setCellValueExplicit('I' . $currentDataRow, (string)$row[8], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $currentDataRow, (string)$row[9], DataType::TYPE_STRING);

            // Highlight refunds with red background
            $isRefund = $transaction->booking && $transaction->booking->refund;
            if ($isRefund) {
                $sheet->getStyle('A' . $currentDataRow . ':AD' . $currentDataRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEE2E2'] // Light red
                    ]
                ]);
            } else {
                // Add zebra striping for non-refund rows
                if ($index % 2 == 0) {
                    $sheet->getStyle('A' . $currentDataRow . ':AD' . $currentDataRow)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9FAFB']
                        ]
                    ]);
                }
            }

            // Format as currency for price columns (O, P, Q, R, S, T, U, V, W, X, Y)
            $currencyColumns = ['O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];
            foreach ($currencyColumns as $col) {
                $sheet->getStyle($col . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        $dataEndRow = $excel->getCurrentRow() - 1;

        // Style data rows with borders
        if ($payments->count() > 0) {
            $sheet->getStyle('A' . $dataStartRow . ':AD' . $dataEndRow)->applyFromArray([
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
        $excel->addInfoRow('SUMMARY REPORT', [
            'bold' => true,
            'bgColor' => 'D1FAE5',
            'textColor' => '059669',
            'fontSize' => 12,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'AD',
        ]);

        $excel->addEmptyRow();

        // Total Revenue using new method
        $excel->addSummaryRow('TOTAL REVENUE:', $totalRevenue, [
            'labelColumn' => 'A',
            'valueColumn' => 'W',
            'labelEndColumn' => 'V',
            'bgColor' => 'D1FAE5',
            'textColor' => '059669',
        ]);

        // Format revenue as currency
        $summaryRowNum = $excel->getCurrentRow() - 1;
        $sheet->getStyle('W' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

        // Total Records
        $excel->addInfoRow('Total Payments: ' . $payments->count() . ' | Refunds: ' . $totalRefunds, [
            'bold' => true,
            'fontSize' => 10,
            'textColor' => '6B7280',
            'align' => Alignment::HORIZONTAL_RIGHT,
            'endColumn' => 'AD',
        ]);

        // Add footer
        $excel->addEmptyRow();
        $excel->addInfoRow('Report generated by Booking Management System', [
            'fontSize' => 9,
            'italic' => true,
            'textColor' => '9CA3AF',
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'AD',
        ]);

        // Freeze panes at header row
        $sheet->freezePane('A' . ($actualHeaderRow + 1));

        // Download
        return $excel->download($filename);
    }

    private function getPayments()
    {
        $query = Transaction::with([
                'payment.verifiedBy',
                'property',
                'room',
                'booking.refund',
                'user'
            ])
            ->whereHas('payment')
            ->where('transaction_status', 'paid')
            ->orderByDesc('paid_at');

        // Apply filters
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = $this->filters['start_date'];
            $endDate = $this->filters['end_date'];

            $query->whereBetween('paid_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        if (!empty($this->filters['property_id'])) {
            $query->where('property_id', $this->filters['property_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('transaction_code', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    private function mapPayment($transaction, $no, $invoiceNumber): array
    {
        $payment = $transaction->payment;

        // Detect refund
        $isRefund = $transaction->booking && $transaction->booking->refund;
        $refundSuffix = $isRefund ? ' [REFUND]' : '';

        // Get duration
        $duration = 0;
        $bookingType = $transaction->booking_type ?? 'daily';
        if ($bookingType === 'monthly') {
            $duration = $transaction->booking_months ?? 0;
        } else {
            $duration = $transaction->booking_days ?? 0;
        }

        // Get price per unit (daily or monthly rate)
        $pricePerUnit = 0;
        if ($bookingType === 'monthly') {
            $pricePerUnit = $transaction->monthly_price ?? 0;
        } else {
            $pricePerUnit = $transaction->daily_price ?? 0;
        }

        // Calculate DPP (Dasar Pengenaan Pajak) - price without VAT 11%
        // DPP = Price / 1.11
        $dppKamarPerUnit = $pricePerUnit / 1.11;

        // Subtotal = Duration * DPP Kamar Per Unit
        $subtotal = $duration * $dppKamarPerUnit;

        // Discount (from voucher)
        $diskon = $transaction->discount_amount ?? 0;
        $dppDiskon = $diskon / 1.11;

        // Parking
        $parkir = $transaction->parking_fee ?? 0;
        $dppParkir = $parkir / 1.11;

        // Deposit Fee
        $depositFee = $transaction->deposit_fee ?? 0;
        $dppDepositFee = $depositFee / 1.11;

        // VATT 11% = (Subtotal - DPP Diskon + DPP Parkir + DPP Deposit Fee) * 11%
        $vatt = ($subtotal - $dppDiskon + $dppParkir + $dppDepositFee) * 0.11;

        // Grand Total calculation for display
        // Note: Using actual grandtotal_price from transaction for accuracy
        $grandTotal = $transaction->grandtotal_price ?? 0;

        // Deposit
        $deposit = $transaction->deposit ?? 0;

        // Service Fee
        $serviceFee = $transaction->service_fees ?? 1;

        // Room type (name from m_rooms)
        $roomType = '-';
        if ($transaction->room) {
            $roomType = $transaction->room->name ?? '-';
        }

        // Room number (no from m_rooms)
        $roomNumber = $transaction->room ? ($transaction->room->no ?? '-') : '-';

        // Get verified_by username
        $verifiedBy = '-';
        if ($payment && $payment->verifiedBy) {
            $verifiedBy = $payment->verifiedBy->username ?? '-';
        }

        // Format notes with refund info
        $notes = $payment ? ($payment->notes ?? '') : '';
        if ($isRefund && $transaction->booking->refund) {
            $refundInfo = $transaction->booking->refund;
            $refundDate = Carbon::parse($refundInfo->refund_date)->format('d M Y');
            $refundText = "REFUNDED on {$refundDate}";

            if ($refundInfo->reason) {
                $refundText .= " - Reason: {$refundInfo->reason}";
            }

            if ($refundInfo->amount) {
                $refundText .= " - Amount: Rp " . number_format($refundInfo->amount, 0, ',', '.');
            }

            $notes = $notes ? $notes . " | " . $refundText : $refundText;
        }

        // Get NIK from user if registered
        $nik = $transaction->user ? ($transaction->user->nik ?? '-') : '-';

        return [
            $no,                                                                                    // No
            $invoiceNumber,                                                                         // Invoice Number
            $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-', // Invoice Date
            $transaction->transaction_code ?? '-',                                                  // Transaction Code
            $transaction->property_name ?? '-',                                                     // Property Name
            $roomType,                                                                              // Room Type
            $roomNumber,                                                                            // Room Number
            $transaction->user_name ?? '-',                                                         // Tenant Name
            $nik,                                                                                   // NIK
            $transaction->user_phone_number ?? '-',                                                 // Mobile Number
            $transaction->user_email ?? '-',                                                        // Email
            $transaction->check_in ? Carbon::parse($transaction->check_in)->format('d M Y') : '-', // Check In
            $transaction->check_out ? Carbon::parse($transaction->check_out)->format('d M Y') : '-', // Check Out
            $duration . ' ',                                                                        // Duration
            round($pricePerUnit, 0),                                                               // Price Kamar Per Unit
            round($dppKamarPerUnit, 0),                                                            // DPP Kamar Per Unit
            round($subtotal, 0),                                                                   // Subtotal
            round($diskon, 0),                                                                     // Diskon
            round($dppDiskon, 0),                                                                  // DPP Diskon
            round($parkir, 0),                                                                     // Parkir
            round($dppParkir, 0),                                                                  // DPP Parkir
            round($vatt, 0),                                                                       // VATT 11%
            round($grandTotal, 0),                                                                 // Grand Total
            round($deposit + $depositFee, 0),                                                      // Deposit (includes deposit fee)
            round($serviceFee, 0),                                                                 // Service Fee
            'Paid',                                                                                // Payment Status
            ($transaction->is_renewal == 1) ? 'Perpanjangan' : '',                                // Status Sewa
            $verifiedBy,                                                                           // Verified By
            $payment && $payment->verified_at ? Carbon::parse($payment->verified_at)->format('d M Y H:i') : '-', // Verified Date
            $notes,                                                                                // Notes
        ];
    }

    private function getFilterTexts(): array
    {
        $filters = [];

        // Date Range
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->format('d M Y');
            $endDate = Carbon::parse($this->filters['end_date'])->format('d M Y');
            $filters[] = 'Payment Period: ' . $startDate . ' - ' . $endDate;
        }

        // Property
        if (!empty($this->filters['property_id'])) {
            $property = Property::find($this->filters['property_id']);
            if ($property) {
                $filters[] = 'Property: ' . $property->name;
            }
        }

        // Search
        if (!empty($this->filters['search'])) {
            $filters[] = 'Search Keyword: "' . $this->filters['search'] . '"';
        }

        return $filters;
    }
}
