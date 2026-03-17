<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip API key check for health check endpoint
        if ($request->is('api/health-check')) {
            return $next($request);
        }

        // Get the API key from the X-API-KEY header
        $apiKey = $request->header('X-API-KEY');
        $expectedApiKey = config('services.api.key');
        
        // Log the request for debugging
        // \Log::info('API Request', [
        //     'path' => $request->path(),
        //     'full_url' => $request->fullUrl(),
        //     'api_key' => $apiKey,
        //     'expected_key' => $expectedApiKey,
        //     'headers' => $request->headers->all()
        // ]);
        
        if (empty($apiKey) || $apiKey !== $expectedApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or missing API KEY',
                'documentation' => 'Please contact our staff for assistance'
            ], 401);
        }
        
        return $next($request);
    }
}
