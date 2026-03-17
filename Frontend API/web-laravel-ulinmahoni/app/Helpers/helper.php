<?php
namespace App\Helpers;

class Helper{
    public static function formatCurrency($value, $beforeValue = "IDR ", $afterValue = "")
    {
        $output = $beforeValue . number_format($value, 0, ',', '.') . $afterValue;
        return $output;
    }
    
    /**
     * Get the current application version.
     *
     * @return string
     */
    public static function version()
    {
        return \App\Helpers\VersionHelper::getVersion();
    }
}