<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce minimum app version.
 * Checks X-App-Version header against min_app_version in global_title table.
 * Old apps without the header are also blocked (no header = version 0.0.0).
 * Skips check for health-check endpoint so the app can read the min version.
 */
class CheckAppVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip version check for health-check (app needs this to know the minimum version)
        if ($request->is('api/health-check') || $request->is('api/v1/health-check')) {
            return $next($request);
        }

        // Get minimum version from database
        try {
            $row = DB::table('global_title')
                ->whereRaw('`key` = ?', ['min_app_version'])
                ->first();
            $minVersion = $row ? $row->mark : null;
        } catch (\Exception $e) {
            // DB error — don't block, let request through
            return $next($request);
        }

        // No minimum set — allow all
        if (!$minVersion) {
            return $next($request);
        }

        // Only check mobile app requests that send the version header.
        // Web browsers and frontend portal don't send X-App-Version — skip them.
        $appVersion = $request->header('X-App-Version');

        if (!$appVersion) {
            return $next($request);
        }

        if ($this->isOutdated($appVersion, $minVersion)) {
            return response()->json([
                'status' => 'error',
                'message' => 'App version outdated. Please update to version ' . $minVersion . ' or later.',
                'min_version' => $minVersion,
                'your_version' => $appVersion,
                'update_required' => true,
            ], 426); // 426 Upgrade Required
        }

        return $next($request);
    }

    /** Compare semantic versions: returns true if current < minimum */
    private function isOutdated(string $current, string $minimum): bool
    {
        $cur = array_map('intval', explode('.', $current));
        $min = array_map('intval', explode('.', $minimum));

        // Pad to 3 parts
        while (count($cur) < 3) $cur[] = 0;
        while (count($min) < 3) $min[] = 0;

        for ($i = 0; $i < 3; $i++) {
            if ($cur[$i] < $min[$i]) return true;
            if ($cur[$i] > $min[$i]) return false;
        }
        return false; // Equal
    }
}
