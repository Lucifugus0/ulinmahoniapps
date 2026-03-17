<?php

namespace App\Exports;

use App\Models\DepositFeeTransaction;
use App\Models\Property;
use App\Services\ExcelService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DepositReportExport
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
        $excel->addTitleSection('LAPORAN KEUANGAN DEPOSIT', [
            'bgColor' => '7C3AED',
            'textColor' => 'FFFFFF',
            'fontSize' => 20,
            'height' => 40,
            'endColumn' => 'N',
        ]);

        $excel->addInfoRow('Generated on: ' . now()->format('l, d F Y - H:i:s'), [
            'fontSize' => 10,
            'textColor' => '6B7280',
            'italic' => true,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();

        $filterTexts = $this->getFilterTexts();
        $excel->addFilterSection($filterTexts);

        $excel->addInfoRow('', []);

        // Headers (14 columns)
        $headers = [
            'No',
            'Invoice ID',
            'Order ID',
            'Property Name',
            'Room',
            'Tenant Name',
            'Phone',
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
        $sheet->getStyle('A' . $actualHeaderRow . ':N' . $actualHeaderRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7C3AED']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '6D28D9']
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
            'H' => 18,
            'I' => 16,
            'J' => 18,
            'K' => 14,
            'L' => 18,
            'M' => 18,
            'N' => 30,
        ];

        foreach ($columnWidths as $col => $width) {
            $excel->setColumnWidth($col, $width);
        }

        $dataStartRow = $excel->getCurrentRow();

        foreach ($payments as $index => $deposit) {
            $row = $this->mapPayment($deposit, $index + 1);
            $currentDataRow = $excel->getCurrentRow();

            $excel->addRow($row);

            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $currentDataRow . ':N' . $currentDataRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }

            // Format currency column H
            $sheet->getStyle('H' . $currentDataRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        $dataEndRow = $excel->getCurrentRow() - 1;

        if ($payments->count() > 0) {
            $sheet->getStyle('A' . $dataStartRow . ':N' . $dataEndRow)->applyFromArray([
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
            'bgColor' => 'EDE9FE',
            'textColor' => '7C3AED',
            'fontSize' => 12,
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();

        $excel->addSummaryRow('TOTAL DEPOSIT REVENUE:', $totalRevenue, [
            'labelColumn' => 'A',
            'valueColumn' => 'H',
            'labelEndColumn' => 'G',
            'bgColor' => 'EDE9FE',
            'textColor' => '7C3AED',
        ]);

        $summaryRowNum = $excel->getCurrentRow() - 1;
        $sheet->getStyle('H' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

        $excel->addInfoRow('Total Deposit Payments: ' . $payments->count(), [
            'bold' => true,
            'fontSize' => 10,
            'textColor' => '6B7280',
            'align' => Alignment::HORIZONTAL_RIGHT,
            'endColumn' => 'N',
        ]);

        $excel->addEmptyRow();
        $excel->addInfoRow('Report generated by Booking Management System', [
            'fontSize' => 9,
            'italic' => true,
            'textColor' => '9CA3AF',
            'align' => Alignment::HORIZONTAL_CENTER,
            'endColumn' => 'N',
        ]);

        $sheet->freezePane('A' . ($actualHeaderRow + 1));

        return $excel->download($filename);
    }

    private function getPayments()
    {
        $query = DepositFeeTransaction::with([
                'transaction',
                'transaction.property',
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
            $propertyId = $this->filters['property_id'];
            $query->whereHas('transaction', function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('user_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get();
    }

    private function mapPayment($deposit, $no): array
    {
        $verifiedByName = '-';
        if ($deposit->verifiedBy) {
            $verifiedByName = $deposit->verifiedBy->first_name . ' ' . $deposit->verifiedBy->last_name;
        }

        $propertyName = '-';
        $roomName = '-';
        $tenantName = '-';
        $tenantPhone = '-';

        if ($deposit->transaction) {
            $propertyName = $deposit->transaction->property_name ?? '-';
            $tenantName = $deposit->transaction->user_name ?? '-';
            $tenantPhone = $deposit->transaction->user_phone_number ?? '-';
            if ($deposit->transaction->room) {
                $roomName = $deposit->transaction->room->name;
            }
        }

        return [
            $no,
            $deposit->invoice_id ?? '-',
            $deposit->order_id ?? '-',
            $propertyName,
            $roomName,
            $tenantName,
            $tenantPhone,
            round($deposit->fee_amount ?? 0, 0),
            $deposit->transaction_date ? Carbon::parse($deposit->transaction_date)->format('d M Y') : '-',
            $deposit->paid_at ? Carbon::parse($deposit->paid_at)->format('d M Y H:i') : '-',
            'Paid',
            $verifiedByName,
            $deposit->verified_at ? Carbon::parse($deposit->verified_at)->format('d M Y H:i') : '-',
            $deposit->notes ?? '-',
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
