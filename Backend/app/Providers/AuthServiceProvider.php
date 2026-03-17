<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        $permissions = [
            'Management',
            'view_dashboard',
            'view_bookings',
            'view_all_bookings',
            'view_pending_bookings',
            'view_confirmed_bookings',
            'view_checkins',
            'view_checkouts',
            'view_completed_bookings',
            'view_change_room',
            'properties',
            'view_properties',
            'view_property_facilities',
            'view_deposit_fees',
            'parking',
            'view_parking_fees',
            'view_parking',
            'rooms',
            'view_rooms',
            'view_room_availability',
            'view_vouchers',
            'view_promo_banners',
            'view_room_facilities',
            'view_door_locks',
            'view_customers',
            'manage_chat',
            'financial',
            'view_payments',
            'view_parking_payments',
            'view_deposit_payments',
            'view_refunds',
            'view_invoices',
            'view_reports',
            'view_booking_report',
            'view_payment_report',
            'view_parking_report',
            'view_deposit_report',
            'view_rented_rooms_report',
            'Settings',
            'view_users',
            'manage_roles',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
