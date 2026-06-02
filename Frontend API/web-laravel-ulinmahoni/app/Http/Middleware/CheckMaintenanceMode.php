<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// <!-- Middleware to check if maintenance mode is enabled in the global_title table.
//      When maintenance_mode = '1', all web requests are redirected to the maintenance page.
//      API requests return a JSON response with maintenance status for the mobile app. -->
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // <!-- Skip maintenance check for the maintenance page itself to avoid redirect loop -->
        if ($request->is('maintenance')) {
            return $next($request);
        }

        try {
            $maintenance = DB::table('global_title')
                ->whereRaw('`key` = ?', ['maintenance_mode'])
                ->first();

            if ($maintenance && $maintenance->mark === '1') {
                // <!-- API requests get JSON response so mobile app can show maintenance popup -->
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json([
                        'status' => 'maintenance',
                        'message' => 'The application is currently under maintenance. Please try again later.',
                    ], 503);
                }

                // <!-- Web requests get redirected to the maintenance landing page -->
                return response()->view('maintenance', [], 503);
            }
        } catch (\Exception $e) {
            // <!-- If DB connection fails, allow request through (don't block on DB errors) -->
        }

        return $next($request);
    }
}
