<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Management Section
            'Management',
            // Dashboard
            'view_dashboard',

            // Bookings
            'view_bookings',
            'view_all_bookings',
            'view_pending_bookings',
            'view_confirmed_bookings',
            'view_checkins',
            'view_checkouts',
            'view_completed_bookings',
            'view_change_room',

            // Properties
            'properties',
            'view_properties',
            'view_property_facilities',
            'view_deposit_fees',

            //Parking
            'parking',
            'view_parking_fees',
            'view_parking',

            // Rooms
            'rooms',
            'view_rooms',
            'view_room_facilities',
            'view_door_locks',

            // Customers
            'view_customers',

            // Rentals
            'view_room_availability',

            // Vouchers
            'view_vouchers',

            // Chat
            'manage_chat',

            // Financial
            'financial',

            // Payments
            'view_payments',
            'view_parking_payments',
            'view_deposit_payments',
            'view_refunds',

            // Reports
            'view_reports',
            'view_booking_report',
            'view_payment_report',
            'view_parking_report',
            'view_deposit_report',
            'view_rented_rooms_report',

            // Settings
            'Settings',

            // User Management
            'view_users',
            'manage_roles',
            'manage_settings',

            // Promo Banners
            'view_promo_banners',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
