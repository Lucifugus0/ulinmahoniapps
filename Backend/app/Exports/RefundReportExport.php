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
 * Refund Report Excel export.
 *
 * <!-- Restructured to follow the "Template Report refund" layout: a two-row
 *      header where the "Refund Amount" group spans a 3-column breakdown
 *      (Harga Kamar / Diskon / Parkir), plus invoice metadata, the original
 *      invoice figures, refund percentage, deposit and total. A "Keterangan"
 *      (legend) block is appended below the data, mirroring the template.
 *      The Reason column is preserved as the final column (after Status). -->
 *
 * Columns A..W = 23 columns:
 *   A Invoice No.   B Booking ID    C Invoice Date  D Refund Date
 *   E Guest Name    F NIK           G Property Name H Room Type
 *   I Room Number   J Check In      K Check Out     L Harga Kamar
 *   M Diskon        N Parkir        O Invoice Amount P Refund (%)
 *   Q-S Refund Amount {Harga Kamar / Diskon / Parkir}
 *   T Deposit       U Total Refund  V Status        W Reason
 */
class RefundReportExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithColumnWidths
{
    protected $filters;
    protected $rowCount = 0;
    protected $totalRefunded = 0;

    /** Last spreadsheet column used by the report (A..W). */
    const LAST_COL = 'W';

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

    /**
     * Top header row. The "Refund Amount" group label sits in column Q and is
     * merged across Q:S in registerEvents(); the empty strings reserve R and S.
     */
    public function headings(): array
    {
        return [
            'Invoice No.',
            'Booking ID',
            'Invoice Date',
            'Refund Date',
            'Guest Name',
            'NIK',
            'Property Name',
            'Room Type',
            'Room Number',
            'Check In',
            'Check Out',
            'Harga Kamar',
            'Diskon',
            'Parkir',
            'Invoice Amount',
            'Refund (%)',
            'Refund Amount', // Q — merged across Q:S
            '',              // R
            '',              // S
            'Deposit',
            'Total Refund',
            'Status',
            'Reason',
        ];
    }

    public function map($refund): array
    {
        $transaction = $refund->transaction;

        // <!-- Guest name: user first+last -> fallback to transaction.user_name -->
        $guestName = '-';
        $nik = '-';
        if ($transaction) {
            $user = $transaction->user;
            if ($user) {
                $full = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $guestName = $full !== '' ? $full : ($transaction->user_name ?? '-');
                $nik = $user->nik ?? '-';
            } else {
                $guestName = $transaction->user_name ?? '-';
            }
        }

        $propertyName = $transaction && $transaction->property ? $transaction->property->name : '-';
        $room = $transaction ? $transaction->room : null;
        $roomType = $room ? ($room->type ?? $room->name ?? '-') : '-';
        $roomNumber = $room ? ($room->no ?? '-') : '-';

        // <!-- Original invoice figures: pull from the linked transaction. -->
        $invoiceAmount = $transaction ? (float) ($transaction->grandtotal_price ?? 0) : 0;
        $roomPrice = 0;
        $discount = 0;
        $parkingFee = 0;
        if ($transaction) {
            $roomPrice = (float) ($transaction->room_price ?? 0);
            if ($roomPrice <= 0) {
                $roomPrice = (float) ($transaction->subtotal_before_discount ?? 0);
            }
            $discount = (float) ($transaction->discount_amount ?? 0);
            $parkingFee = (float) ($transaction->parking_fee ?? 0);
        }

        $totalRefund = (float) ($refund->amount ?? 0);

        // <!-- Refund % is not stored on t_refund — derive it from the refunded
        //      total against the original invoice amount (fraction, e.g. 0.75). -->
        $refundPct = $invoiceAmount > 0 ? $totalRefund / $invoiceAmount : 0;

        // <!-- Refund Amount breakdown: prefer the stored per-component refund
        //      values; when absent, compute base figure x refund %. -->
        $roomRefund = (float) ($refund->room_refund ?? 0);
        if ($roomRefund <= 0) {
            $roomRefund = round($roomPrice * $refundPct);
        }

        // t_refund has no discount-refund column — always derived.
        $discountRefund = round($discount * $refundPct);

        // other_refund is treated as the parking portion; fall back to computed.
        $parkingRefund = (float) ($refund->other_refund ?? 0);
        if ($parkingRefund <= 0) {
            $parkingRefund = round($parkingFee * $refundPct);
        }

        // Deposit returned: stored deposit_refund, else the transaction deposit.
        $deposit = (float) ($refund->deposit_refund ?? 0);
        if ($deposit <= 0 && $transaction) {
            $deposit = (float) ($transaction->deposit_fee ?? 0);
        }

        return [
            $transaction ? ($transaction->invoice_number ?? '-') : '-',
            $refund->id_booking,
            $transaction && $transaction->transaction_date
                ? Carbon::parse($transaction->transaction_date)->format('d M Y')
                : '-',
            $refund->refund_date ? Carbon::parse($refund->refund_date)->format('d M Y H:i') : '-',
            $guestName,
            $nik,
            $propertyName,
            $roomType,
            $roomNumber,
            $transaction && $transaction->check_in
                ? Carbon::parse($transaction->check_in)->format('d M Y')
                : '-',
            $transaction && $transaction->check_out
                ? Carbon::parse($transaction->check_out)->format('d M Y')
                : '-',
            $roomPrice,
            $discount,
            $parkingFee,
            $invoiceAmount,
            $refundPct,
            $roomRefund,
            $discountRefund,
            $parkingRefund,
            $deposit,
            $totalRefund,
            $refund->status ?? '-',
            $refund->reason ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Invoice No.
            'B' => 20,  // Booking ID
            'C' => 14,  // Invoice Date
            'D' => 18,  // Refund Date
            'E' => 22,  // Guest Name
            'F' => 20,  // NIK
            'G' => 24,  // Property Name
            'H' => 16,  // Room Type
            'I' => 14,  // Room Number
            'J' => 14,  // Check In
            'K' => 14,  // Check Out
            'L' => 16,  // Harga Kamar
            'M' => 14,  // Diskon
            'N' => 14,  // Parkir
            'O' => 16,  // Invoice Amount
            'P' => 12,  // Refund (%)
            'Q' => 16,  // Refund Amount - Harga Kamar
            'R' => 14,  // Refund Amount - Diskon
            'S' => 14,  // Refund Amount - Parkir
            'T' => 14,  // Deposit
            'U' => 18,  // Total Refund
            'V' => 14,  // Status
            'W' => 36,  // Reason
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $last = self::LAST_COL;

                // <!-- Title/filter block sizing: 1 title + 1 generated + 1 blank
                //      + 1 "FILTERS APPLIED:" + N filter lines + 1 blank spacer. -->
                $filterTexts = $this->getFilterTexts();
                $filterLines = max(count($filterTexts), 1);
                $topRows = 4 + $filterLines + 1;

                // Reserve rows above the heading for title + filters.
                $sheet->insertNewRowBefore(1, $topRows);

                // Heading row produced by WithHeadings is now at $headerTopRow.
                $headerTopRow = $topRows + 1;
                // Insert one extra row for the "Refund Amount" sub-headers.
                $subHeaderRow = $headerTopRow + 1;
                $sheet->insertNewRowBefore($subHeaderRow, 1);

                $dataStartRow = $subHeaderRow + 1;
                $dataEndRow = $dataStartRow + $this->rowCount - 1;

                // ---- Title --------------------------------------------------
                $sheet->setCellValue('A1', 'REFUND REPORT');
                $sheet->mergeCells('A1:' . $last . '1');
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

                // ---- Generated date ----------------------------------------
                $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y, H:i'));
                $sheet->mergeCells('A2:' . $last . '2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ---- Filters ------------------------------------------------
                $filterRow = 4;
                $sheet->setCellValue('A' . $filterRow, 'FILTERS APPLIED:');
                $sheet->getStyle('A' . $filterRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                ]);

                $filterRow++;
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

                // ---- Two-row header ----------------------------------------
                // "Refund Amount" group label spans Q:S on the top row; the
                // sub-header row carries the per-component labels.
                $sheet->mergeCells('Q' . $headerTopRow . ':S' . $headerTopRow);
                $sheet->setCellValue('Q' . $subHeaderRow, 'Harga Kamar');
                $sheet->setCellValue('R' . $subHeaderRow, 'Diskon');
                $sheet->setCellValue('S' . $subHeaderRow, 'Parkir');

                // Every other column header is merged vertically across both rows.
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                          'L', 'M', 'N', 'O', 'P', 'T', 'U', 'V', 'W'] as $col) {
                    $sheet->mergeCells($col . $headerTopRow . ':' . $col . $subHeaderRow);
                }

                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => '1F2937'], // dark text on light-green band
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        // Green, Accent 6, Lighter 60% — matches the report template.
                        'startColor' => ['rgb' => 'C6E0B4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '70AD47'],
                        ],
                    ],
                ];
                $sheet->getStyle('A' . $headerTopRow . ':' . $last . $subHeaderRow)
                    ->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerTopRow)->setRowHeight(22);
                $sheet->getRowDimension($subHeaderRow)->setRowHeight(20);

                // ---- Data rows ---------------------------------------------
                if ($this->rowCount > 0) {
                    $sheet->getStyle('A' . $dataStartRow . ':' . $last . $dataEndRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                    ]);

                    for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                        if (($row - $dataStartRow) % 2 == 0) {
                            $sheet->getStyle('A' . $row . ':' . $last . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'EBF3E1'], // light-green zebra stripe
                                ],
                            ]);
                        }
                    }

                    // Currency columns: Harga Kamar, Diskon, Parkir, Invoice
                    // Amount, the 3-part Refund Amount breakdown, Deposit, Total.
                    foreach (['L', 'M', 'N', 'O', 'Q', 'R', 'S', 'T', 'U'] as $col) {
                        $sheet->getStyle($col . $dataStartRow . ':' . $col . $dataEndRow)
                            ->getNumberFormat()->setFormatCode('#,##0');
                    }

                    // Refund (%) column — stored as a fraction, shown as percent.
                    $sheet->getStyle('P' . $dataStartRow . ':P' . $dataEndRow)
                        ->getNumberFormat()->setFormatCode('0.00%');
                }

                // ---- Summary ------------------------------------------------
                $summaryRow = $dataEndRow + 2;

                $sheet->setCellValue('A' . $summaryRow, 'TOTAL REFUNDED:');
                $sheet->mergeCells('A' . $summaryRow . ':T' . $summaryRow);
                $sheet->getStyle('A' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $sheet->setCellValue('U' . $summaryRow, $this->totalRefunded);
                $sheet->getStyle('U' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F513D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6E9C6'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '70AD47'],
                        ],
                    ],
                ]);
                $sheet->getStyle('U' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

                // ---- Total records -----------------------------------------
                $recordsRow = $summaryRow + 1;
                $sheet->setCellValue('A' . $recordsRow, 'Total Records: ' . $this->rowCount);
                $sheet->mergeCells('A' . $recordsRow . ':' . $last . $recordsRow);
                $sheet->getStyle('A' . $recordsRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'italic' => true, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // ---- Keterangan (legend) block -----------------------------
                // <!-- Mirrors the template's column-glossary so readers know
                //      what each grouped figure refers to. -->
                $this->writeLegend($sheet, $recordsRow + 2);

                $sheet->freezePane('A' . $dataStartRow);
            },
        ];
    }

    /**
     * Writes the "Keterangan :" legend block starting at the given row.
     */
    private function writeLegend($sheet, int $startRow): void
    {
        $sheet->setCellValue('A' . $startRow, 'Keterangan :');
        $sheet->getStyle('A' . $startRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
        ]);

        $legend = [
            ['Invoice No, Invoice Date', 'Mengacu pada invoice awal terbit'],
            ['Refund Date', 'Tanggal refund'],
            ['Harga Kamar', 'Harga kamar include tax'],
            ['Diskon', 'Diskon include tax'],
            ['Parkir', 'Harga parkir include tax'],
            ['Invoice Amount', 'Mengacu pada nominal invoice awal terbit'],
            ['Refund (%)', 'Persentase refund terhadap nominal invoice'],
            ['Refund Amount', 'Break down perhitungan yang akan direfund'],
            ['Deposit', 'Nilai deposit (dikembalikan full)'],
            ['Total Refund', 'Perhitungan total refund setelah ditambahkan deposit'],
            ['Reason', 'Alasan refund diajukan'],
        ];

        $row = $startRow + 1;
        foreach ($legend as $entry) {
            $sheet->setCellValue('A' . $row, $entry[0]);
            $sheet->setCellValue('B' . $row, '→ ' . $entry[1]);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['size' => 10, 'bold' => true, 'color' => ['rgb' => '374151']],
            ]);
            $sheet->getStyle('B' . $row)->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
            ]);
            $row++;
        }
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
