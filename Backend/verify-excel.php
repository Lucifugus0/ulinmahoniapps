<?php
/**
 * Script untuk verifikasi instalasi Laravel Excel
 * Jalankan script ini di cPanel untuk mengecek apakah package sudah terinstall dengan benar
 */

echo "=== Laravel Excel Verification Script ===\n\n";

// 1. Check if vendor directory exists
echo "1. Checking vendor directory...\n";
if (file_exists(__DIR__ . '/vendor')) {
    echo "   ✓ Vendor directory exists\n\n";
} else {
    echo "   ✗ Vendor directory NOT found!\n";
    echo "   → Run: composer install\n\n";
    exit(1);
}

// 2. Check if maatwebsite/excel package exists
echo "2. Checking maatwebsite/excel package...\n";
if (file_exists(__DIR__ . '/vendor/maatwebsite/excel')) {
    echo "   ✓ Package directory exists\n\n";
} else {
    echo "   ✗ Package NOT found!\n";
    echo "   → Run: composer require maatwebsite/excel\n\n";
    exit(1);
}

// 3. Check autoload files
echo "3. Checking autoload files...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✓ Autoload file exists\n\n";
    require __DIR__ . '/vendor/autoload.php';
} else {
    echo "   ✗ Autoload file NOT found!\n";
    echo "   → Run: composer dump-autoload\n\n";
    exit(1);
}

// 4. Check if class can be loaded
echo "4. Checking if Excel Facade can be loaded...\n";
if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
    echo "   ✓ Excel Facade class found\n\n";
} else {
    echo "   ✗ Excel Facade class NOT found!\n\n";
}

// 5. Check if ExcelServiceProvider exists
echo "5. Checking ExcelServiceProvider...\n";
if (class_exists('Maatwebsite\Excel\ExcelServiceProvider')) {
    echo "   ✓ ExcelServiceProvider class found\n\n";
} else {
    echo "   ✗ ExcelServiceProvider class NOT found!\n\n";
}

// 6. Check config file
echo "6. Checking config file...\n";
if (file_exists(__DIR__ . '/config/excel.php')) {
    echo "   ✓ Config file exists\n\n";
} else {
    echo "   ✗ Config file NOT found!\n";
    echo "   → Run: php artisan vendor:publish --provider=\"Maatwebsite\\Excel\\ExcelServiceProvider\" --tag=config\n\n";
}

// 7. Check composer.json
echo "7. Checking composer.json...\n";
if (file_exists(__DIR__ . '/composer.json')) {
    $composerData = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
    if (isset($composerData['require']['maatwebsite/excel'])) {
        echo "   ✓ Package is in composer.json (version: " . $composerData['require']['maatwebsite/excel'] . ")\n\n";
    } else {
        echo "   ✗ Package NOT in composer.json!\n\n";
    }
}

echo "=== Verification Complete ===\n";
echo "\nIf you see any errors above, follow the suggested commands.\n";
echo "Then run: php artisan config:clear && php artisan cache:clear\n";
