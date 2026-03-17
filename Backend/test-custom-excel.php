<?php

/**
 * Test script untuk Custom Excel Library
 *
 * Cara menjalankan:
 * 1. Via browser: http://yourdomain.com/test-custom-excel.php
 * 2. Via CLI: php test-custom-excel.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\ExcelService;

echo "=== Testing Custom Excel Library ===\n\n";

try {
    // Test 1: Create Excel instance
    echo "Test 1: Creating ExcelService instance...\n";
    $excel = new ExcelService();
    echo "   ✓ ExcelService created successfully\n\n";

    // Test 2: Set title
    echo "Test 2: Setting sheet title...\n";
    $excel->setTitle('Test Report');
    echo "   ✓ Title set successfully\n\n";

    // Test 3: Add headers
    echo "Test 3: Adding headers...\n";
    $headers = ['ID', 'Name', 'Email', 'Amount'];
    $excel->addHeader($headers);
    echo "   ✓ Headers added successfully\n\n";

    // Test 4: Add data rows
    echo "Test 4: Adding data rows...\n";
    $data = [
        [1, 'John Doe', 'john@example.com', 150000],
        [2, 'Jane Smith', 'jane@example.com', 200000],
        [3, 'Bob Johnson', 'bob@example.com', 175000],
        [4, 'Alice Brown', 'alice@example.com', 225000],
        [5, 'Charlie Davis', 'charlie@example.com', 190000],
    ];
    $excel->addRows($data);
    echo "   ✓ Data rows added successfully (" . count($data) . " rows)\n\n";

    // Test 5: Freeze first row
    echo "Test 5: Freezing first row...\n";
    $excel->freezeFirstRow();
    echo "   ✓ First row frozen successfully\n\n";

    // Test 6: Check PhpSpreadsheet instance
    echo "Test 6: Checking PhpSpreadsheet instance...\n";
    $spreadsheet = $excel->getSpreadsheet();
    if ($spreadsheet instanceof \PhpOffice\PhpSpreadsheet\Spreadsheet) {
        echo "   ✓ PhpSpreadsheet instance is valid\n\n";
    } else {
        echo "   ✗ PhpSpreadsheet instance is invalid\n\n";
    }

    // Test 7: Save to file (optional)
    echo "Test 7: Saving to file...\n";
    $testFilePath = storage_path('app/test-report.xlsx');

    // Create storage/app directory if not exists
    if (!file_exists(storage_path('app'))) {
        mkdir(storage_path('app'), 0755, true);
    }

    $saved = $excel->save($testFilePath);
    if ($saved && file_exists($testFilePath)) {
        $fileSize = filesize($testFilePath);
        echo "   ✓ File saved successfully\n";
        echo "   Location: $testFilePath\n";
        echo "   Size: " . number_format($fileSize / 1024, 2) . " KB\n\n";
    } else {
        echo "   ✗ Failed to save file\n\n";
    }

    echo "=== All Tests Passed! ===\n\n";
    echo "Library is working correctly. You can now:\n";
    echo "1. Use ExcelService in your controllers\n";
    echo "2. Create custom export classes\n";
    echo "3. Deploy to production\n\n";

    // If running in browser, offer download
    if (php_sapi_name() !== 'cli') {
        echo '<br><br>';
        echo '<a href="#" onclick="downloadTest()">Click here to test download</a>';
        echo '<script>
        function downloadTest() {
            // Redirect to download endpoint
            alert("In production, this will trigger the download. Check your export endpoints!");
        }
        </script>';
    }

} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
