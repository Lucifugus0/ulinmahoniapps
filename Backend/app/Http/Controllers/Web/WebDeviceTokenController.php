<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

/**
 * Handles web push notification device token registration for session-authenticated users.
 * Separate from API token-based auth used by mobile apps.
 */
class WebDeviceTokenController extends Controller
{
    /** Register or update a web push FCM token for the authenticated user */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:500',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = $request->user();

        /* Upsert: update if token exists for this user, otherwise create new entry */
        DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $request->token],
            [
                'device_type' => 'web',
                'device_name' => $request->device_name ?? substr($request->userAgent(), 0, 100),
                'is_active' => true,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Web push token registered']);
    }

    /** Deactivate all web tokens for the authenticated user (called on logout) */
    public function destroy(Request $request)
    {
        $user = $request->user();

        /* Deactivate rather than delete — preserves history for debugging */
        DeviceToken::where('user_id', $user->id)
            ->where('device_type', 'web')
            ->update(['is_active' => false]);

        return response()->json(['status' => 'success', 'message' => 'Web push tokens deactivated']);
    }
}
