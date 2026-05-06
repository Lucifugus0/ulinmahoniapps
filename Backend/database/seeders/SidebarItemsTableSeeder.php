<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarItem;
use App\Models\Permission;

class SidebarItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * <!-- Restructured sidebar: flat top-level groups (no section headers),
     *      collapsible groups for Bookings, Finance, Promo, Reports, Masters, App Management.
     *      Route names unchanged — only display names and hierarchy changed. -->
     *
     * @return void
     */
    public function run()
    {
        // Clear existing items
        SidebarItem::truncate();

        // Get all permissions
        $permissions = Permission::all()->pluck('id', 'name')->toArray();

        // =====================
        // Standalone Items
        // =====================

        // <!-- Dashboard: standalone top-level item -->
        SidebarItem::create([
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'permission_id' => $permissions['view_dashboard'] ?? null,
            'parent_id' => null,
            'order' => 1
        ]);

        // <!-- Room Availability: standalone top-level item -->
        SidebarItem::create([
            'name' => 'Room Availability',
            'route' => 'room-availability.index',
            'permission_id' => $permissions['view_room_availability'] ?? null,
            'parent_id' => null,
            'order' => 2
        ]);

        // <!-- Chat / Customer Service: NOT registered in Access Rights —
        //      Customer Service group (Tickets + Broadcasts) is available to all users
        //      and does not require role-based access control. -->

        // =====================
        // Bookings Group (order 4)
        // =====================
        // <!-- Bookings: collapsible group containing all booking-related pages -->
        $bookings = SidebarItem::create([
            'name' => 'Bookings',
            'route' => null,
            'permission_id' => $permissions['view_bookings'] ?? null,
            'parent_id' => null,
            'order' => 4,
        ]);

        SidebarItem::create([
            'name' => 'All Bookings',
            'route' => 'bookings.index',
            'permission_id' => $permissions['view_all_bookings'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 1
        ]);

        SidebarItem::create([
            'name' => 'Pending',
            'route' => 'pendings.index',
            'permission_id' => $permissions['view_pending_bookings'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 2
        ]);

        SidebarItem::create([
            'name' => 'Confirmed Bookings',
            'route' => 'newReserv.index',
            'permission_id' => $permissions['view_confirmed_bookings'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 3
        ]);

        SidebarItem::create([
            'name' => 'Checked In',
            'route' => 'checkin.index',
            'permission_id' => $permissions['view_checkins'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 4
        ]);

        SidebarItem::create([
            'name' => "Today's Check Out",
            'route' => 'checkout.index',
            'permission_id' => $permissions['view_checkouts'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 5
        ]);

        SidebarItem::create([
            'name' => 'Completed',
            'route' => 'completed.index',
            'permission_id' => $permissions['view_completed_bookings'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 6
        ]);

        // <!-- Renamed from "Change Room" to "Change Booking" -->
        SidebarItem::create([
            'name' => 'Change Booking',
            'route' => 'changerooom.index',
            'permission_id' => $permissions['view_change_room'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 7
        ]);

        // <!-- Modify Booking: dedicated `view_modify_booking` permission (separated from `view_change_room`
        //      on 2026-05-05) so admins can grant/revoke independently of Change Booking. -->
        SidebarItem::create([
            'name' => 'Modify Booking',
            'route' => 'modifyBooking.index',
            'permission_id' => $permissions['view_modify_booking'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 8
        ]);

        // <!-- Door Lock: moved from Rooms/Units to Bookings group -->
        SidebarItem::create([
            'name' => 'Door Lock',
            'route' => 'door-locks.index',
            'permission_id' => $permissions['view_door_locks'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 9
        ]);

        // <!-- Parking Management: moved from standalone to Bookings group -->
        SidebarItem::create([
            'name' => 'Parking Management',
            'route' => 'parking.index',
            'permission_id' => $permissions['view_parking'] ?? null,
            'parent_id' => $bookings->id,
            'order' => 10
        ]);

        // =====================
        // Finance Group (order 5)
        // =====================
        // <!-- Finance: collapsible group for all payment-related pages (renamed from Financial/Payments) -->
        $finance = SidebarItem::create([
            'name' => 'Finance',
            'route' => null,
            'permission_id' => null,
            'parent_id' => null,
            'order' => 5,
        ]);

        // <!-- Renamed from "Parking" to "Parking Entry" -->
        SidebarItem::create([
            'name' => 'Parking Entry',
            'route' => 'admin.parking-payments.index',
            'permission_id' => $permissions['view_parking_payments'] ?? null,
            'parent_id' => $finance->id,
            'order' => 1
        ]);

        // <!-- Renamed from "Deposit" to "Deposit Entry" -->
        SidebarItem::create([
            'name' => 'Deposit Entry',
            'route' => 'admin.deposit-payments.index',
            'permission_id' => $permissions['view_deposit_payments'] ?? null,
            'parent_id' => $finance->id,
            'order' => 2
        ]);

        // <!-- Renamed from "Transaction" to "Booking Payment" -->
        SidebarItem::create([
            'name' => 'Booking Payment',
            'route' => 'admin.payments.index',
            'permission_id' => $permissions['view_payments'] ?? null,
            'parent_id' => $finance->id,
            'order' => 3
        ]);

        SidebarItem::create([
            'name' => 'Refunds',
            'route' => 'admin.refunds.index',
            'permission_id' => $permissions['view_refunds'] ?? null,
            'parent_id' => $finance->id,
            'order' => 4
        ]);

        // =====================
        // Promo Group (order 6)
        // =====================
        // <!-- Promo: collapsible group for vouchers and banners -->
        $promo = SidebarItem::create([
            'name' => 'Promo',
            'route' => null,
            'permission_id' => null,
            'parent_id' => null,
            'order' => 6,
        ]);

        // <!-- Renamed from "Vouchers" to "Voucher Management" -->
        SidebarItem::create([
            'name' => 'Voucher Management',
            'route' => 'vouchers.index',
            'permission_id' => $permissions['view_vouchers'] ?? null,
            'parent_id' => $promo->id,
            'order' => 1
        ]);

        // <!-- Renamed from "Promo Banners" to "Banner Management" -->
        SidebarItem::create([
            'name' => 'Banner Management',
            'route' => 'promo-banners.index',
            'permission_id' => $permissions['view_promo_banners'] ?? null,
            'parent_id' => $promo->id,
            'order' => 2
        ]);

        // =====================
        // Reports Group (order 7)
        // =====================
        // <!-- Reports: collapsible group, moved from Financial section to top-level -->
        $reports = SidebarItem::create([
            'name' => 'Reports',
            'route' => null,
            'permission_id' => $permissions['view_reports'] ?? null,
            'parent_id' => null,
            'order' => 7
        ]);

        SidebarItem::create([
            'name' => 'Booking Report',
            'route' => 'reports.booking.index',
            'permission_id' => $permissions['view_booking_report'] ?? null,
            'parent_id' => $reports->id,
            'order' => 1
        ]);

        SidebarItem::create([
            'name' => 'Transaction Report',
            'route' => 'reports.payment.index',
            'permission_id' => $permissions['view_payment_report'] ?? null,
            'parent_id' => $reports->id,
            'order' => 2
        ]);

        SidebarItem::create([
            'name' => 'Parking Report',
            'route' => 'reports.parking.index',
            'permission_id' => $permissions['view_parking_report'] ?? null,
            'parent_id' => $reports->id,
            'order' => 3
        ]);

        SidebarItem::create([
            'name' => 'Deposit Report',
            'route' => 'reports.deposit.index',
            'permission_id' => $permissions['view_deposit_report'] ?? null,
            'parent_id' => $reports->id,
            'order' => 4
        ]);

        SidebarItem::create([
            'name' => 'Rented Rooms Report',
            'route' => 'reports.rented-rooms.index',
            'permission_id' => $permissions['view_rented_rooms_report'] ?? null,
            'parent_id' => $reports->id,
            'order' => 5
        ]);

        // =====================
        // Masters Group (order 8)
        // =====================
        // <!-- Masters: collapsible group consolidating Properties, Rooms/Units, and other master data -->
        $masters = SidebarItem::create([
            'name' => 'Masters',
            'route' => null,
            'permission_id' => null,
            'parent_id' => null,
            'order' => 8,
        ]);

        // <!-- Cities: was "Master Cities" under Properties -->
        SidebarItem::create([
            'name' => 'Cities',
            'route' => 'cityProperty.index',
            'permission_id' => $permissions['view_cities'] ?? null,
            'parent_id' => $masters->id,
            'order' => 1
        ]);

        // <!-- Properties: renamed from "Master Properties" -->
        SidebarItem::create([
            'name' => 'Properties',
            'route' => 'properties.index',
            'permission_id' => $permissions['view_properties'] ?? null,
            'parent_id' => $masters->id,
            'order' => 2
        ]);

        // <!-- Property's Facilities: renamed from "Master Facilities" under Properties -->
        SidebarItem::create([
            'name' => "Property's Facilities",
            'route' => 'facilityProperty.index',
            'permission_id' => $permissions['view_property_facilities'] ?? null,
            'parent_id' => $masters->id,
            'order' => 3
        ]);

        // <!-- Property's Deposit & Parking: renamed from "Deposit Fee Management" + "Parking Fees" -->
        SidebarItem::create([
            'name' => "Property's Deposit & Parking",
            'route' => 'property-fees.index',
            'permission_id' => $permissions['view_deposit_fees'] ?? null,
            'parent_id' => $masters->id,
            'order' => 4
        ]);

        // <!-- Property's Rooms: renamed from "Master Rooms" -->
        SidebarItem::create([
            'name' => "Property's Rooms",
            'route' => 'rooms.index',
            'permission_id' => $permissions['view_rooms'] ?? null,
            'parent_id' => $masters->id,
            'order' => 5
        ]);

        // <!-- Room Types: renamed from "Master Room Types".
        //      Uses dedicated `view_room_types` permission (separated from `view_rooms` on 2026-05-05)
        //      so admins can grant/revoke Room Types independently of Property's Rooms in the
        //      Access Rights modal. Previously both shared `view_rooms`, which made unchecking
        //      one item silently grant access via the other on save+reload. -->
        SidebarItem::create([
            'name' => 'Room Types',
            'route' => 'roomNameTypes.index',
            'permission_id' => $permissions['view_room_types'] ?? null,
            'parent_id' => $masters->id,
            'order' => 6
        ]);

        // <!-- Room's Facilities: renamed from "Master Facilities" under Rooms/Units -->
        SidebarItem::create([
            'name' => "Room's Facilities",
            'route' => 'facilityRooms.index',
            'permission_id' => $permissions['view_room_facilities'] ?? null,
            'parent_id' => $masters->id,
            'order' => 7
        ]);

        // <!-- Daily Pricing Management: renamed from "Master Calendar".
        //      Dedicated `view_daily_pricing` permission (separated from `view_properties` on 2026-05-05). -->
        SidebarItem::create([
            'name' => 'Daily Pricing Management',
            'route' => 'calendar.index',
            'permission_id' => $permissions['view_daily_pricing'] ?? null,
            'parent_id' => $masters->id,
            'order' => 8
        ]);

        // <!-- Customers: moved from standalone under Management to Masters group -->
        SidebarItem::create([
            'name' => 'Customers',
            'route' => 'customers.index',
            'permission_id' => $permissions['view_customers'] ?? null,
            'parent_id' => $masters->id,
            'order' => 9
        ]);

        // <!-- Users: moved from Settings section to Masters group -->
        SidebarItem::create([
            'name' => 'Users',
            'route' => 'users-newManagement',
            'permission_id' => $permissions['view_users'] ?? null,
            'parent_id' => $masters->id,
            'order' => 10
        ]);

        // =====================
        // App Management Group (order 9)
        // =====================
        // <!-- App Management: collapsible group replacing Settings section -->
        $appManagement = SidebarItem::create([
            'name' => 'App Management',
            'route' => null,
            'permission_id' => null,
            'parent_id' => null,
            'order' => 9,
        ]);

        // <!-- Master Role: promoted directly under App Management on 2026-05-06.
        //      Previously nested under an "Access Management" sub-group with a sibling "User Access"
        //      entry, but User Access (route: user-access.edit → pages/settings/user-access-management.blade.php)
        //      is a legacy page with no sidebar link, replaced by the Access Rights modal on this very
        //      page (master-role-management). Removing User Access left Access Management with one
        //      child, so the sub-group was flattened — Master Role now sits directly under App Management,
        //      matching the actual sidebar structure (sidebar.blade.php:828-834). -->
        SidebarItem::create([
            'name' => 'Access Rights',
            'route' => 'master-role-management',
            'permission_id' => $permissions['manage_roles'] ?? null,
            'parent_id' => $appManagement->id,
            'order' => 1
        ]);

        // <!-- Content Management: tagline & video editor — added under App Management group.
        //      Dedicated `view_content_management` permission (separated from `manage_settings`
        //      on 2026-05-05) so admins can grant/revoke independently of system Settings. -->
        SidebarItem::create([
            'name' => 'Content Management',
            'route' => 'content-management.index',
            'permission_id' => $permissions['view_content_management'] ?? null,
            'parent_id' => $appManagement->id,
            'order' => 2
        ]);

        // <!-- Settings: moved from section header to sub-item of App Management -->
        SidebarItem::create([
            'name' => 'Settings',
            'route' => 'users.show',
            'permission_id' => $permissions['manage_settings'] ?? null,
            'parent_id' => $appManagement->id,
            'order' => 3
        ]);

        // <!-- Maintenance Mode is intentionally NOT registered in sidebar_items.
        //      It's a super-admin-only feature: sidebar visibility is gated by
        //      `Auth::user()->email === 'admin_tsno@gmail.com'` in sidebar.blade.php
        //      (System Management group) and the route is similarly gated in
        //      CheckPermission.php:47-52. There's no useful permission to grant here,
        //      so it doesn't belong in the Access Rights modal. -->


        // <!-- Note: Users is now under Masters group (order 8, child 10) instead of Settings section.
        //      Users route (users-newManagement) is registered there with view_users permission.
        //      Also note: users-management route is kept as a parent mapping in CheckPermission middleware. -->
    }
}
