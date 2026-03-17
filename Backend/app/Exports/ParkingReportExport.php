<?php

namespace App\Exports;

use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Services\ExcelService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ParkingReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function export(string $filename)
    {
        $payments = $this->getPayments();

        $totalRevenue = $payments->sum(function ($transaction) {
            return $transaction->fee_amount ?? 0;
        });

        $excel = new ExcelService();
        $sheet = $excel->getActiveSheet();

        // Title
        $excel->addTitleSection('LAPORAN KEUANGAN PARKIR', [
            'bgColor' => '2563EB',
            'textColor' => 'FFFFFF',
            'fontSize' => 20,
            'height' => 40,
            'endColumn' => 'P',
        ]);

        $excel->addInfoRow('Generated on: ' . now()->format('l, d F Y - H:i:s'), [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'italic' => true,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'P',
        ]);

        $excel->addEmptyRow();

        $filterTexts = $this->getFilterTexts();
        $excel->addFilterSection($filterTexts);

        $excel->addInfoRow('', []);

        // Headers (16 columns)
        $headers = [
            'No',
            'Invoice ID',
            'Order ID',
            'Property Name',
            'Room',
            'Tenant Name',
            'Phone',
            'Parking Type',
            'Vehicle Plate',
            'Fee Amount',
            'Transaction Date',
            'Paid At',
            'Payment Status',
            'Verified By',
            'Verified Date',
            'Notes',
        ];

        $excel->addHeader($headers);

        $actualHeaderRow = $excel->getCurrentRow() - 1;
        $sheet->getStyle('A' . $actualHeaderRow . ':P' . $actualHeaderRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1D4ED8']
                ]
            ]
        ]);
        $sheet->getRowDimension($actualHeaderRow)->setRowHeight(35);

        // Column widths
        $columnWidths = [
            'A' => 6,
            'B' => 20,
            'C' => 20,
            'D' => 22,
            'E' => 15,
            'F' => 20,
            'G' => 16,
            'H' => 15,
            'I' => 15,
            'J' => 18,
            'K' => 16,
            'L' => 18,
            'M' => 14,
            'N' => 18,
            'O' => 18,
            'P' => 30,
        ];

        foreach ($columnWidths as $col => $width) {
            $excel->setColumnWidth($col, $width);
        }

        $dataStartRow = $excel->getCurrentRow();

        foreach ($payments as $index => $transaction) {
            $row = $this->mapPayment($transaction, $index + 1);
            $currentDataRow = $excel->getCurrentRow();

            $excel->addRow($row);

            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $currentDataRow . ':P' . $currentDataRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }

            // Format currency column J
            $sheet->getStyle('J' . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        $dataEndRow = $excel->getCurrentRow() - 1;

        if ($payments->count() > 0) {
            $sheet->getStyle('A' . $dataStartRow . ':P' . $dataEndRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB']
                    ]
                ]
            ]);
        }

        $excel->addEmptyRow(2);

        $excel->addInfoRow('SUMMARY REPORT', [
            'bold' => true,
            'bgColor' => 'DBEAFE',
            'textColor' => '2563EB',
            'fontSize' => 12,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'P',
        ]);

        $excel->addEmptyRow();

        $excel->addSummaryRow('TOTAL PARKING REVENUE:', $totalRevenue, [
            'labelColumn' => 'A',
            'valueColumn' => 'J',
            'labelEndColumn' => 'I',
            'bgColor' => 'DBEAFE',
            'textColor' => '2563EB',
        ]);

        $summaryRowNum = $excel->getCurrentRow() - 1;
        $sheet->getStyle('J' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

        $excel->addInfoRow('Total Parking Payments: ' . $payments->count(), [
            'bold' => true,
            'fontSize' => 10,
            'textColor' => '6B7280',
            'align' => Alignment::HORIZONTAL_RIGHT,
            'endColumn' => 'P',
        ]);

        $excel->addEmptyRow();
        $excel->addInfoRow('Report generated by Booking Management System', [
            'fontSize' => 9,
            'italic' => true,
            'textColor' => '9CA3AF',
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'P',
        ]);

        $sheet->freezePane('A' . ($actualHeaderRow + 1));

        return $excel->download($filename);
    }

    private function getPayments()
    {
        $query = ParkingFeeTransaction::with([
                'property',
                'transaction',
                'transaction.room',
                'verifiedBy',
            ])
            ->where('transaction_status', 'paid')
            ->orderByDesc('paid_at');

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('paid_at', [
                $this->filters['start_date'] . ' 00:00:00',
                $this->filters['end_date'] . ' 23:59:59'
            ]);
        }

        if (!empty($this->filters['property_id'])) {
            $query->where('property_id', $this->filters['property_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    private function mapPayment($transaction, $no): array
    {
        $verifiedByName = '-';
        if ($transaction->verifiedBy) {
            $verifiedByName = $transaction->verifiedBy->first_name . ' ' . $transaction->verifiedBy->last_name;
        }

        $propertyName = $transaction->property ? $transaction->property->name : '-';
        $roomName = '-';
        if ($transaction->transaction && $transaction->transaction->room) {
            $roomName = $transaction->transaction->room->name;
        }

        return [
            $no,
            $transaction->invoice_id ?? '-',
            $transaction->order_id ?? '-',
            $propertyName,
            $roomName,
            $transaction->user_name ?? '-',
            $transaction->user_phone ?? '-',
            ucfirst($transaction->parking_type ?? '-'),
            $transaction->vehicle_plate ?? '-',
            round($transaction->fee_amount ?? 0, 0),
            $transaction->transaction_date ? Carbon::parse($transaction->transaction_date)->format('d M Y') : '-',
            $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-',
            'Paid',
            $verifiedByName,
            $transaction->verified_at ? Carbon::parse($transaction->verified_at)->format('d M Y H:i') : '-',
            $transaction->notes ?? '-',
        ];
    }

    private function getFilterTexts(): array
    {
        $filters = [];

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->format('d M Y');
            $endDate = Carbon::parse($this->filters['end_date'])->format('d M Y');
            $filters[] = 'Payment Period: ' . $startDate . ' - ' . $endDate;
        }

        if (!empty($this->filters['property_id'])) {
            $property = Property::find($this->filters['property_id']);
            if ($property) {
                $filters[] = 'Property: ' . $property->name;
            }
        }

        if (!empty($this->filters['search'])) {
            $filters[] = 'Search Keyword: "' . $this->filters['search'] . '"';
        }

        return $filters;
    }
}
