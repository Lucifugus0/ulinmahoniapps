<?php

namespace App\Listeners;

use App\Models\DeviceToken;
use Illuminate\Auth\Events\Logout;

/**
 * Deactivate all web push device tokens when a user logs out.
 * Prevents push notifications to browsers the user has signed out of.
 */
class DeactivateWebPushTokensOnLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            /* Only deactivate web tokens — mobile tokens are managed by the mobile app */
            DeviceToken::where('user_id', $event->user->id)
                ->where('device_type', 'web')
                ->update(['is_active' => false]);
        }
    }
}
