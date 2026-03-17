<?php
namespace App\Helpers;
class Divider{
    public static function divider($value, $beforeValue = " ", $afterValue = "")
    {
        $output = $beforeValue . number_format($value, 0, ',', '.') . $afterValue;
        return $output;
    }
    
}