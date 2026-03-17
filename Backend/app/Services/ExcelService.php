<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelService
{
    protected Spreadsheet $spreadsheet;
    protected $activeSheet;
    protected int $currentRow = 1;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->activeSheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Set sheet title
     */
    public function setTitle(string $title): self
    {
        $this->activeSheet->setTitle($title);
        return $this;
    }

    /**
     * Add header row with styling
     */
    public function addHeader(array $headers): self
    {
        $column = 'A';
        foreach ($headers as $header) {
            $cellCoordinate = $column . $this->currentRow;
            $this->activeSheet->setCellValue($cellCoordinate, $header);

            // Style header
            $this->activeSheet->getStyle($cellCoordinate)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Auto width
            $this->activeSheet->getColumnDimension($column)->setAutoSize(true);

            $column++;
        }

        $this->currentRow++;
        return $this;
    }

    /**
     * Add data rows
     */
    public function addRows(array $data): self
    {
        foreach ($data as $row) {
            $column = 'A';
            foreach ($row as $value) {
                $cellCoordinate = $column . $this->currentRow;
                $this->activeSheet->setCellValue($cellCoordinate, $value);

                // Style data cell
                $this->activeSheet->getStyle($cellCoordinate)->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                $column++;
            }
            $this->currentRow++;
        }

        return $this;
    }

    /**
     * Add single row
     */
    public function addRow(array $row): self
    {
        return $this->addRows([$row]);
    }

    /**
     * Set column width
     */
    public function setColumnWidth(string $column, float $width): self
    {
        $this->activeSheet->getColumnDimension($column)->setWidth($width);
        return $this;
    }

    /**
     * Freeze first row
     */
    public function freezeFirstRow(): self
    {
        $this->activeSheet->freezePane('A2');
        return $this;
    }

    /**
     * Apply auto filter
     */
    public function autoFilter(?string $range = null): self
    {
        if ($range === null) {
            $highestColumn = $this->activeSheet->getHighestColumn();
            $highestRow = $this->activeSheet->getHighestRow();
            $range = 'A1:' . $highestColumn . $highestRow;
        }

        $this->activeSheet->setAutoFilter($range);
        return $this;
    }

    /**
     * Download as Excel file
     */
    public function download(string $filename): StreamedResponse
    {
        // Ensure filename has .xlsx extension
        if (!str_ends_with($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return new StreamedResponse(
            function () {
                $writer = new Xlsx($this->spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Save to file
     */
    public function save(string $path): bool
    {
        try {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get spreadsheet instance for advanced usage
     */
    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * Get active sheet for advanced usage
     */
    public function getActiveSheet()
    {
        return $this->activeSheet;
    }

    /**
     * Add title section with custom styling
     */
    public function addTitleSection(string $title, array $options = []): self
    {
        $startColumn = $options['startColumn'] ?? 'A';
        $endColumn = $options['endColumn'] ?? 'L';
        $bgColor = $options['bgColor'] ?? '1E3A8A'; // Dark blue
        $textColor = $options['textColor'] ?? 'FFFFFF';
        $fontSize = $options['fontSize'] ?? 18;
        $height = $options['height'] ?? 35;

        $cellRange = $startColumn . $this->currentRow . ':' . $endColumn . $this->currentRow;

        $this->activeSheet->setCellValue($startColumn . $this->currentRow, $title);
        $this->activeSheet->mergeCells($cellRange);
        $this->activeSheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => $fontSize,
                'color' => ['rgb' => $textColor],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bgColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $this->activeSheet->getRowDimension($this->currentRow)->setRowHeight($height);

        $this->currentRow++;
        return $this;
    }

    /**
     * Add subtitle or info row
     */
    public function addInfoRow(string $text, array $options = []): self
    {
        $startColumn = $options['startColumn'] ?? 'A';
        $endColumn = $options['endColumn'] ?? 'L';
        $bgColor = $options['bgColor'] ?? null;
        $textColor = $options['textColor'] ?? '374151';
        $fontSize = $options['fontSize'] ?? 11;
        $bold = $options['bold'] ?? false;
        $italic = $options['italic'] ?? false;
        $align = $options['align'] ?? Alignment::HORIZONTAL_LEFT;

        $cellRange = $startColumn . $this->currentRow . ':' . $endColumn . $this->currentRow;

        $this->activeSheet->setCellValue($startColumn . $this->currentRow, $text);
        $this->activeSheet->mergeCells($cellRange);

        $styleArray = [
            'font' => [
                'bold' => $bold,
                'italic' => $italic,
                'size' => $fontSize,
                'color' => ['rgb' => $textColor],
            ],
            'alignment' => [
                'horizontal' => $align,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        if ($bgColor) {
            $styleArray['fill'] = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bgColor],
            ];
        }

        $this->activeSheet->getStyle($cellRange)->applyFromArray($styleArray);

        $this->currentRow++;
        return $this;
    }

    /**
     * Add filter info section with box styling
     */
    public function addFilterSection(array $filters): self
    {
        if (empty($filters)) {
            $this->addInfoRow('📋 No filters applied - showing all data', [
                'italic' => true,
                'textColor' => '6B7280',
                'fontSize' => 10,
            ]);
            $this->currentRow++;
            return $this;
        }

        // Filter header
        $this->addInfoRow('🔍 APPLIED FILTERS', [
            'bold' => true,
            'bgColor' => 'F3F4F6',
            'textColor' => '1F2937',
            'fontSize' => 11,
        ]);

        // Filter items with indentation
        foreach ($filters as $filterText) {
            $this->addInfoRow('   ' . $filterText, [
                'fontSize' => 10,
                'textColor' => '4B5563',
            ]);
        }

        $this->currentRow++;
        return $this;
    }

    /**
     * Add company/organization info
     */
    public function addCompanyInfo(string $companyName, array $additionalInfo = []): self
    {
        $this->addInfoRow($companyName, [
            'bold' => true,
            'fontSize' => 14,
            'textColor' => '1F2937',
            'align' => Alignment::HORIZONTAL_CENTER,
        ]);

        foreach ($additionalInfo as $info) {
            $this->addInfoRow($info, [
                'fontSize' => 10,
                'textColor' => '6B7280',
                'align' => Alignment::HORIZONTAL_CENTER,
            ]);
        }

        $this->currentRow++;
        return $this;
    }

    /**
     * Add empty row for spacing
     */
    public function addEmptyRow(int $count = 1): self
    {
        $this->currentRow += $count;
        return $this;
    }

    /**
     * Add summary row at current position
     */
    public function addSummaryRow(string $label, $value, array $options = []): self
    {
        $labelColumn = $options['labelColumn'] ?? 'A';
        $valueColumn = $options['valueColumn'] ?? 'J';
        $labelEndColumn = $options['labelEndColumn'] ?? 'I';
        $bgColor = $options['bgColor'] ?? 'D1FAE5';
        $textColor = $options['textColor'] ?? '059669';
        $borderColor = $options['borderColor'] ?? '059669';

        // Label
        $labelRange = $labelColumn . $this->currentRow . ':' . $labelEndColumn . $this->currentRow;
        $this->activeSheet->setCellValue($labelColumn . $this->currentRow, $label);
        $this->activeSheet->mergeCells($labelRange);
        $this->activeSheet->getStyle($labelRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => $textColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Value
        $this->activeSheet->setCellValue($valueColumn . $this->currentRow, $value);
        $this->activeSheet->getStyle($valueColumn . $this->currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => $textColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bgColor],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => $borderColor],
                ],
            ],
        ]);

        $this->currentRow++;
        return $this;
    }

    /**
     * Get current row number
     */
    public function getCurrentRow(): int
    {
        return $this->currentRow;
    }

    /**
     * Set current row number
     */
    public function setCurrentRow(int $row): self
    {
        $this->currentRow = $row;
        return $this;
    }

    /**
     * Create new instance (static factory)
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Quick export from array
     */
    public static function export(array $data, array $headers, string $filename, string $title = 'Sheet1')
    {
        $excel = self::create();

        $excel->setTitle($title)
            ->addHeader($headers)
            ->addRows($data)
            ->freezeFirstRow();

        return $excel->download($filename);
    }
}
