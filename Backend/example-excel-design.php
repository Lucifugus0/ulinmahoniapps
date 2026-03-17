<?php

/**
 * Contoh Penggunaan Excel dengan Desain Premium
 *
 * File ini mendemonstrasikan cara membuat Excel export dengan:
 * - Header perusahaan
 * - Judul dengan styling
 * - Informasi filter
 * - Tabel data dengan zebra striping
 * - Summary dengan highlight
 * - Footer
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\ExcelService;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

echo "=== Creating Premium Excel Report ===\n\n";

try {
    // Initialize Excel Service
    $excel = new ExcelService();
    $sheet = $excel->getActiveSheet();

    // 1. MAIN TITLE
    echo "1. Adding main title...\n";
    $excel->addTitleSection('📊 LAPORAN BOOKING KAMAR', [
        'bgColor' => '1E3A8A',      // Dark Blue
        'textColor' => 'FFFFFF',     // White
        'fontSize' => 20,
        'height' => 40,
    ]);

    // 2. GENERATION INFO
    echo "2. Adding generation info...\n";
    $excel->addInfoRow('Generated on: ' . date('l, d F Y - H:i:s'), [
        'fontSize' => 10,
        'textColor' => '6B7280',
        'italic' => true,
        'align' => Alignment::HORIZONTAL_CENTER,
    ]);

    $excel->addEmptyRow();

    // 3. FILTER SECTION
    echo "3. Adding filter information...\n";
    $filters = [
        '📅 Booking Period: 01 Dec 2025 - 31 Dec 2025',
        '📊 Status: ✅ Checked-In',
        '🏢 Property: Gedung Rektorat',
    ];
    $excel->addFilterSection($filters);

    $excel->addInfoRow('', []); // Separator

    // 4. TABLE HEADERS
    echo "4. Adding table headers...\n";
    $headers = [
        'Date',
        'Booking No',
        'Customer Name',
        'Property',
        'Room',
        'Check In',
        'Check Out',
        'Amount',
        'Status'
    ];
    $excel->addHeader($headers);

    // Custom styling for header
    $headerRow = $excel->getCurrentRow() - 1;
    $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 11,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '3730A3']
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
    $sheet->getRowDimension($headerRow)->setRowHeight(28);

    // Set column widths
    $excel->setColumnWidth('A', 12);
    $excel->setColumnWidth('B', 18);
    $excel->setColumnWidth('C', 25);
    $excel->setColumnWidth('D', 25);
    $excel->setColumnWidth('E', 20);
    $excel->setColumnWidth('F', 15);
    $excel->setColumnWidth('G', 15);
    $excel->setColumnWidth('H', 15);
    $excel->setColumnWidth('I', 15);

    // 5. SAMPLE DATA with Zebra Striping
    echo "5. Adding sample data...\n";
    $sampleData = [
        ['01/12/25', 'BK001', 'John Doe', 'Gedung A', 'R-101', '01/12/25', '03/12/25', 500000, 'Checked-Out'],
        ['02/12/25', 'BK002', 'Jane Smith', 'Gedung B', 'R-201', '02/12/25', '04/12/25', 600000, 'Checked-Out'],
        ['03/12/25', 'BK003', 'Bob Johnson', 'Gedung A', 'R-102', '03/12/25', '05/12/25', 550000, 'Checked-In'],
        ['04/12/25', 'BK004', 'Alice Brown', 'Gedung C', 'R-301', '04/12/25', '06/12/25', 700000, 'Checked-In'],
        ['05/12/25', 'BK005', 'Charlie Davis', 'Gedung B', 'R-202', '05/12/25', '07/12/25', 650000, 'Waiting'],
    ];

    $dataStartRow = $excel->getCurrentRow();

    foreach ($sampleData as $index => $row) {
        $currentRow = $excel->getCurrentRow();
        $excel->addRow($row);

        // Format currency
        $sheet->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

        // Zebra striping
        if ($index % 2 == 0) {
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F9FAFB']
                ]
            ]);
        }
    }

    $dataEndRow = $excel->getCurrentRow() - 1;

    // Add borders to data
    $sheet->getStyle('A' . $dataStartRow . ':I' . $dataEndRow)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'D1D5DB']
            ]
        ]
    ]);

    // 6. SUMMARY SECTION
    echo "6. Adding summary section...\n";
    $excel->addEmptyRow(2);

    $excel->addInfoRow('📈 SUMMARY REPORT', [
        'bold' => true,
        'bgColor' => 'E0E7FF',
        'textColor' => '3730A3',
        'fontSize' => 12,
        'align' => Alignment::HORIZONTAL_CENTER,
    ]);

    $excel->addEmptyRow();

    // Calculate total
    $totalRevenue = 500000 + 600000 + 550000 + 700000 + 650000;

    $excel->addSummaryRow('TOTAL REVENUE:', $totalRevenue, [
        'labelColumn' => 'A',
        'valueColumn' => 'H',
        'labelEndColumn' => 'G',
        'bgColor' => 'D1FAE5',
        'textColor' => '059669',
        'borderColor' => '10B981',
    ]);

    // Format as currency
    $summaryRowNum = $excel->getCurrentRow() - 1;
    $sheet->getStyle('H' . $summaryRowNum)->getNumberFormat()->setFormatCode('Rp #,##0');

    // Total records
    $excel->addInfoRow('Total Records: ' . count($sampleData) . ' bookings', [
        'bold' => true,
        'fontSize' => 10,
        'textColor' => '6B7280',
        'align' => Alignment::HORIZONTAL_RIGHT,
    ]);

    // 7. FOOTER
    echo "7. Adding footer...\n";
    $excel->addEmptyRow();
    $excel->addInfoRow('Report generated by Booking Management System', [
        'fontSize' => 9,
        'italic' => true,
        'textColor' => '9CA3AF',
        'align' => Alignment::HORIZONTAL_CENTER,
    ]);

    // 9. FREEZE PANES
    echo "9. Freezing header panes...\n";
    $sheet->freezePane('A' . ($headerRow + 1));

    // 10. SAVE FILE
    echo "10. Saving file...\n";
    $storageDir = __DIR__ . '/storage/app';
    $filename = $storageDir . '/example-premium-report.xlsx';

    if (!file_exists($storageDir)) {
        mkdir($storageDir, 0755, true);
    }

    $excel->save($filename);

    echo "\n✅ SUCCESS!\n";
    echo "File saved to: $filename\n";
    echo "File size: " . number_format(filesize($filename) / 1024, 2) . " KB\n\n";

    echo "Report includes:\n";
    echo "  ✓ Company information header\n";
    echo "  ✓ Premium title with dark blue background\n";
    echo "  ✓ Generation timestamp\n";
    echo "  ✓ Filter information section\n";
    echo "  ✓ Styled table headers (white on indigo)\n";
    echo "  ✓ Data with zebra striping\n";
    echo "  ✓ Summary section with totals\n";
    echo "  ✓ Professional footer\n";
    echo "  ✓ Frozen header panes\n\n";

    echo "Open the file in Excel/LibreOffice to see the result!\n";

} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
