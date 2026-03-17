<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HealthCheckController extends Controller
{
    private $startTime;

    public function __construct()
    {
        $this->startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
    }

    public function check()
    {
        $status = 'ok';
        $services = [
            'database' => $this->checkDatabase(),
            // 'redis' => $this->checkRedis(),
            'cache' => $this->checkCache(),
        ];

        // If any service is not ok, set overall status to error
        if (in_array('error', $services)) {
            $status = 'error';
        } elseif (in_array('slow', $services)) {
            $status = 'degraded';
        }

        return response()->json([
            'status' => $status,
            'uptime' => $this->getUptime(),
            'timestamp' => now()->toIso8601String(),
            'services' => $services,
        ]);
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return 'ok';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    // private function checkRedis()
    // {
    //     try {
    //         Redis::ping();
    //         return 'ok';
    //     } catch (\Exception $e) {
    //         return 'error';
    //     }
    // }

    private function checkCache()
    {
        try {
            $start = microtime(true);
            $key = 'healthcheck_' . uniqid();
            
            // Test cache write
            Cache::put($key, 'test', 10);
            
            // Test cache read
            $value = Cache::get($key);
            
            // Test cache delete
            Cache::forget($key);
            
            $time = (microtime(true) - $start) * 1000; // Convert to milliseconds
            
            // If cache operations take more than 100ms, consider it slow
            return $time > 100 ? 'slow' : 'ok';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getUptime()
    {
        return round(microtime(true) - $this->startTime, 2) . 's';
    }
}
