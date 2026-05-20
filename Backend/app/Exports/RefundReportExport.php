<?php

namespace App\Exports;

use App\Models\Refund;
use App\Models\Property;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

/**
 * Refund Report Excel export — mirrors BookingReportExport styling so admins
 * recognize the layout. Columns A..O = 15 columns covering refund metadata
 * + breakdown + bank info + admin processing fields.
 */
class RefundReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithColumnWidths
{
    protected $filters;
    protected $rowCount = 0;
    protected $totalRefunded = 0;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Refund::with([
                'transaction.property',
                'transaction.room',
                'transaction.user',
                'requestedBy',
                'processedBy',
            ])
            // <!-- Newest refund first — matches RefundReportController. `id` desc breaks ties
            //      when refund_date (second-precision) is identical for same-second refunds. -->
            ->orderByDesc('refund_date')
            ->orderByDesc('id');

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('refund_date', [
                $this->filters['start_date'] . ' 00:00:00',
                $this->filters['end_date'] . ' 23:59:59',
            ]);
        } elseif (!empty($this->filters['start_date'])) {
            $query->whereDate('refund_date', '>=', $this->filters['start_date']);
        } elseif (!empty($this->filters['end_date'])) {
            $query->whereDate('refund_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['refund_type'])) {
            $query->where('refund_type', $this->filters['refund_type']);
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
                $q->where('id_booking', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('user_name', 'like', "%{$search}%")
                            ->orWhere('order_id', 'like', "%{$search}%");
                    });
            });
        }

        $collection = $query->get();
        $this->rowCount = $collection->count();
        $this->totalRefunded = $collection->sum(function ($refund) {
            return (float) ($refund->amount ?? 0);
        });

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Refund Date',
            'Order ID',
            'Tenant Name',
            'Property',
            'Room',
            'Refund Type',
            'Reason',
            'Room Refund',
            'Deposit Refund',
            'Other Refund',
            'Total Refund',
            'Bank / Account',
            'Status',
            'Processed By',
            'Admin Notes',
        ];
    }

    public function map($refund): array
    {
        $transaction = $refund->transaction;

        // <!-- Tenant name: user first+last -> fallback to transaction.user_name -->
        $tenantName = '-';
        if ($transaction) {
            $user = $transaction->user;
            if ($user) {
                $full = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $tenantName = $full !== '' ? $full : ($transaction->user_name ?? '-');
            } else {
                $tenantName = $transaction->user_name ?? '-';
            }
        }

        $propertyName = $transaction && $transaction->property ? $transaction->property->name : '-';
        $roomName = $transaction && $transaction->room ? $transaction->room->name : '-';

        $bankAccount = '-';
        if ($refund->refund_bank_name || $refund->refund_account_no) {
            $bankAccount = trim(
                ($refund->refund_bank_name ?? '') . ' ' .
                ($refund->refund_account_no ? '- ' . $refund->refund_account_no : '') . ' ' .
                ($refund->refund_account_holder ? '(' . $refund->refund_account_holder . ')' : '')
            );
        }

        $processedBy = $refund->processedBy ? ($refund->processedBy->username ?? '-') : '-';

        return [
            $refund->refund_date ? Carbon::parse($refund->refund_date)->format('d M Y H:i') : '-',
            $refund->id_booking,
            $tenantName,
            $propertyName,
            $roomName,
            ucfirst($refund->refund_type ?? 'admin'),
            $refund->reason ?? '-',
            (float) ($refund->room_refund ?? 0),
            (float) ($refund->deposit_refund ?? 0),
            (float) ($refund->other_refund ?? 0),
            (float) ($refund->amount ?? 0),
            $bankAccount,
            $refund->status ?? '-',
            $processedBy,
            $refund->admin_notes ?? '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // Refund Date
            'B' => 20,  // Order ID
            'C' => 22,  // Tenant Name
            'D' => 24,  // Property
            'E' => 18,  // Room
            'F' => 14,  // Refund Type
            'G' => 30,  // Reason
            'H' => 16,  // Room Refund
            'I' => 16,  // Deposit Refund
            'J' => 16,  // Other Refund
            'K' => 18,  // Total Refund
            'L' => 32,  // Bank / Account
            'M' => 14,  // Status
            'N' => 18,  // Processed By
            'O' => 40,  // Admin Notes
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // <!-- Reserve 6 rows above the heading for title + filters -->
                $sheet->insertNewRowBefore(1, 6);

                // Title
                $sheet->setCellValue('A1', 'REFUND REPORT');
                $sheet->mergeCells('A1:O1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Generated date
                $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y, H:i'));
                $sheet->mergeCells('A2:O2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Filters
                $filterRow = 4;
                $sheet->setCellValue('A' . $filterRow, 'FILTERS APPLIED:');
                $sheet->getStyle('A' . $filterRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                ]);

                $filterRow++;
                $filterTexts = $this->getFilterTexts();
                if (!empty($filterTexts)) {
                    foreach ($filterTexts as $text) {
                        $sheet->setCellValue('A' . $filterRow, $text);
                        $sheet->getStyle('A' . $filterRow)->applyFromArray([
                            'font' => ['size' => 10],
                        ]);
                        $filterRow++;
                    }
                } else {
                    $sheet->setCellValue('A' . $filterRow, 'No filters applied - showing all refunds');
                    $sheet->getStyle('A' . $filterRow)->applyFromArray([
                        'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => '6B7280']],
                    ]);
                }

                // Header row (now at row 7)
                $headerRow = 7;
                $sheet->getStyle('A' . $headerRow . ':O' . $headerRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DC2626'], // refund-themed red
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '991B1B'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(25);

                // Data rows
                $dataStartRow = 8;
                $dataEndRow = $dataStartRow + $this->rowCount - 1;

                if ($this->rowCount > 0) {
                    $sheet->getStyle('A' . $dataStartRow . ':O' . $dataEndRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                    ]);

                    for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                        if (($row - $dataStartRow) % 2 == 0) {
                            $sheet->getStyle('A' . $row . ':O' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FFF5F5'],
                                ],
                            ]);
                        }
                    }

                    // Format currency columns
                    foreach (['H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle($col . $dataStartRow . ':' . $col . $dataEndRow)
                            ->getNumberFormat()->setFormatCode('#,##0');
                    }
                }

                // Summary
                $summaryRow = $dataEndRow + 2;

                $sheet->setCellValue('A' . $summaryRow, 'TOTAL REFUNDED:');
                $sheet->mergeCells('A' . $summaryRow . ':J' . $summaryRow);
                $sheet->getStyle('A' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $sheet->setCellValue('K' . $summaryRow, $this->totalRefunded);
                $sheet->getStyle('K' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'B91C1C']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEE2E2'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'B91C1C'],
                        ],
                    ],
                ]);
                $sheet->getStyle('K' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

                // Total records
                $recordsRow = $summaryRow + 1;
                $sheet->setCellValue('A' . $recordsRow, 'Total Records: ' . $this->rowCount);
                $sheet->mergeCells('A' . $recordsRow . ':O' . $recordsRow);
                $sheet->getStyle('A' . $recordsRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'italic' => true, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $sheet->freezePane('A8');
            },
        ];
    }

    private function getFilterTexts(): array
    {
        $filters = [];

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->format('d M Y');
            $endDate = Carbon::parse($this->filters['end_date'])->format('d M Y');
            $filters[] = '• Refund Date: ' . $startDate . ' to ' . $endDate;
        }

        if (!empty($this->filters['status'])) {
            $filters[] = '• Status: ' . ucfirst($this->filters['status']);
        }

        if (!empty($this->filters['refund_type'])) {
            $filters[] = '• Refund Type: ' . ucfirst($this->filters['refund_type']);
        }

        if (!empty($this->filters['property_id'])) {
            $property = Property::find($this->filters['property_id']);
            if ($property) {
                $filters[] = '• Property: ' . $property->name;
            }
        }

        if (!empty($this->filters['search'])) {
            $filters[] = '• Search: "' . $this->filters['search'] . '"';
        }

        return $filters;
    }
}
