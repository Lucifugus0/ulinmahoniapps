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
            'device_type' => 'required|string|in:ios,android',
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

            return response()->json([
                'status' => 'success',
                'message' => 'Device token registered successfully',
                'data' => $deviceToken
            ], 201);
        } catch (\Exception $e) {
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
