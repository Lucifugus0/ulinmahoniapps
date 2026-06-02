<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class DeviceTokenController extends ApiController
{
    /**
     * Register or update a device token for push notifications.
     * Mobile app calls this on login/startup.
     *
     * POST /api/v1/device-token
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            /* Added 'web' type for browser push notification support */
            'device_type' => 'required|string|in:ios,android,web',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Resolve user from Sanctum Bearer token (auth middleware not applied on API routes)
            $user = $request->user() ?? auth()->user();
            if (!$user) {
                $bearerToken = $request->bearerToken();
                \Log::info('[DeviceToken:store] bearerToken present: ' . ($bearerToken ? 'yes (' . substr($bearerToken, 0, 20) . '...)' : 'no'));
                if ($bearerToken) {
                    $accessToken = PersonalAccessToken::findToken($bearerToken);
                    \Log::info('[DeviceToken:store] findToken result: ' . ($accessToken ? 'found (id=' . $accessToken->id . ', user=' . $accessToken->tokenable_id . ')' : 'null'));
                    $user = $accessToken?->tokenable;
                }
            } else {
                \Log::info('[DeviceToken:store] user resolved via request/auth: id=' . $user->id);
            }

            if (!$user) {
                \Log::warning('[DeviceToken:store] 401 - user not authenticated');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            \Log::info('[DeviceToken:store] About to upsert', [
                'user_id' => $user->id,
                'token_length' => strlen($request->token),
                'token_prefix' => substr($request->token, 0, 20),
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
            ]);

            // Upsert: update if token exists for this user, otherwise create
            $deviceToken = DeviceToken::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'token' => $request->token,
                ],
                [
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                    'is_active' => true,
                ]
            );

            \Log::info('[DeviceToken:store] Upsert result', [
                'id' => $deviceToken->id,
                'wasRecentlyCreated' => $deviceToken->wasRecentlyCreated,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Device token registered successfully',
                'data' => $deviceToken
            ], 201);
        } catch (\Exception $e) {
            \Log::error('[DeviceToken:store] Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error registering device token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a device token (mobile app calls on logout).
     *
     * DELETE /api/v1/device-token
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Resolve user from Sanctum Bearer token (auth middleware not applied on API routes)
            $user = $request->user() ?? auth()->user();
            if (!$user) {
                $bearerToken = $request->bearerToken();
                if ($bearerToken) {
                    $accessToken = PersonalAccessToken::findToken($bearerToken);
                    $user = $accessToken?->tokenable;
                }
            }

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $deleted = DeviceToken::where('user_id', $user->id)
                ->where('token', $request->token)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => $deleted ? 'Device token removed' : 'Token not found',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error removing device token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
