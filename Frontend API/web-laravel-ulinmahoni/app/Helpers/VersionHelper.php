<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class VersionHelper
{
    /**
     * Get the current application version.
     *
     * @return string
     */
    public static function getVersion()
    {
        $versionFile = base_path('version');
        
        if (!File::exists($versionFile)) {
            return '0.0.0';
        }
        
        return trim(File::get($versionFile));
    }
}
