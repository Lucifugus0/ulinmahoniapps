<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Custom Excel Facade
 *
 * @method static \App\Services\ExcelService create()
 * @method static \App\Services\ExcelService setTitle(string $title)
 * @method static \App\Services\ExcelService addHeader(array $headers)
 * @method static \App\Services\ExcelService addRows(array $data)
 * @method static \App\Services\ExcelService addRow(array $row)
 * @method static \App\Services\ExcelService setColumnWidth(string $column, float $width)
 * @method static \App\Services\ExcelService freezeFirstRow()
 * @method static \App\Services\ExcelService autoFilter(?string $range = null)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse download(string $filename)
 * @method static bool save(string $path)
 * @method static \PhpOffice\PhpSpreadsheet\Spreadsheet getSpreadsheet()
 * @method static mixed getActiveSheet()
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse export(array $data, array $headers, string $filename, string $title = 'Sheet1')
 * @method static \App\Services\ExcelService addTitleSection(string $title, array $options = [])
 * @method static \App\Services\ExcelService addInfoRow(string $text, array $options = [])
 * @method static \App\Services\ExcelService addFilterSection(array $filters)
 * @method static \App\Services\ExcelService addCompanyInfo(string $companyName, array $additionalInfo = [])
 * @method static \App\Services\ExcelService addEmptyRow(int $count = 1)
 * @method static \App\Services\ExcelService addSummaryRow(string $label, $value, array $options = [])
 * @method static int getCurrentRow()
 * @method static \App\Services\ExcelService setCurrentRow(int $row)
 *
 * @see \App\Services\ExcelService
 */
class Excel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'excel';
    }
}
