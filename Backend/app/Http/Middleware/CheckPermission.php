<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SidebarItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Allow super admin to access everything
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Get current route name
        $currentRoute = $request->route()->getName();

        // Routes that are always allowed without permission check
        $allowedRoutes = [
            'dashboard',
            'login',
            'logout',
            'user.password.update',
            'user.activity',
            'progress',
            'user.locale.update',
            'room.updateStatus',
        ];

        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        // Routes that are API/data endpoints - check parent page permission
        $apiRoutesPatterns = [
            '/\.getData$/',           // e.g., menus.getData
            '/\.getList$/',           // e.g., users.getList
            '/\.getMainMenus$/',      // e.g., menus.getMainMenus
            '/\.getUserAccess$/',     // e.g., menus.getUserAccess
            '/\.filter$/',            // e.g., bookings.filter
            '/\.store$/',             // e.g., properties.store
            '/\.update$/',            // e.g., properties.update
            '/\.show$/',              // e.g., users.show
            '/\.destroy$/',           // e.g., rooms.destroy
            '/\.destoy$/',            // e.g., rooms.destoy (typo in routes)
            '/\.data$/',              // e.g., room-availability.data
            '/\.export$/',            // e.g., reports.booking.export
            '/\.edit$/',              // e.g., user-access.edit
            '/\.create$/',            // e.g., master-role.create
            '/\.approve$/',           // e.g., admin.payments.approve
            '/\.reject$/',            // e.g., admin.payments.reject
            '/\.cancel$/',            // e.g., admin.bookings.cancel
            '/\.deactivate$/',        // e.g., users.deactivate
            '/\.details$/',           // e.g., bookings.checkin.details
            '/\.regist$/',            // e.g., newReserv.checkin.regist
            '/\.invoice$/',           // e.g., newReserv.checkin.invoice
            '/\.table$/',             // e.g., properties.table
            '/\.bookings$/',          // e.g., customers.bookings, room-availability.bookings
            '/\.toggle-status$/',     // e.g., properties.toggle-status
            '/\.updateStatus$/',      // e.g., properties.updateStatus, users.updateStatus
            '/\.update-status$/',     // e.g., room-availability.update-status
            '/\.update-payment-date$/',     // e.g., admin.payments.update-payment-date
            '/\.update-checkinout$/',       // e.g., admin.payments.update-checkinout
            '/\.check-room-number$/',       // e.g., rooms.check-room-number
            '/\.prices/',             // e.g., rooms.prices.* (any price-related routes)
            '/\.change-price-index$/',      // e.g., rooms.prices.change-price-index
            '/\.date$/',              // e.g., rooms.prices.date
            '/check\.email$/',        // e.g., check.email
            '/-newManagement$/',      // e.g., users-newManagement
            '/-editManagement$/',     // e.g., users-editManagement
            '/-deleteManagement$/',   // e.g., users-deleteManagement
            '/\.permissions$/',       // e.g., master-role.permissions
            '/\.update-permissions$/',// e.g., master-role.update-permissions
            '/\.get$/',               // e.g., dashboard-widgets.get
            '/\.checkin$/',           // e.g., newReserv.checkin, bookings.checkin
            '/\.checkout$/',          // e.g., bookings.checkout
            '/-access-management$/',  // e.g., users-access-management
            '/\.unread-count$/',      // e.g., chat.unread-count
            '/\.conversations-json$/', // e.g., chat.conversations-json
            '/\.checked-in-users$/',  // e.g., chat.checked-in-users
            '/\.send$/',              // e.g., chat.send
            '/\.upload-image$/',      // e.g., chat.upload-image
            '/\.messages\.edit$/',    // e.g., chat.messages.edit
            '/\.find-by-order$/',     // e.g., chat.find-by-order
            '/\.get-details$/',       // e.g., door-locks.get-details
            '/\.passcode$/',          // e.g., door-locks.passcode
        ];

        // Check if current route matches any API pattern
        $isApiRoute = false;
        foreach ($apiRoutesPatterns as $pattern) {
            if (preg_match($pattern, $currentRoute)) {
                $isApiRoute = true;
                break;
            }
        }

        // If it's an API route, check the parent page permission
        if ($isApiRoute) {
            // Extract parent route (e.g., 'bookings.filter' -> 'bookings.index')
            $parentRoute = $this->getParentRoute($currentRoute);

            if ($parentRoute) {
                $sidebarItem = SidebarItem::where('route', $parentRoute)->first();

                // If parent sidebar item exists with permission_id, check permission
                if ($sidebarItem && $sidebarItem->permission_id) {
                    $permissionId = $sidebarItem->permission_id;

                    $hasPermission = DB::table('role_permission')
                        ->where('user_id', $user->id)
                        ->where('permission_id', $permissionId)
                        ->exists();

                    if (!$hasPermission) {
                        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
                    }
                }

                // API route with valid parent mapping - allow access
                // (either permission check passed, or parent not configured for permissions)
                return $next($request);
            }
        }

        // Find sidebar item by route name
        $sidebarItem = SidebarItem::where('route', $currentRoute)->first();

        // SECURITY: Block access if route not found in sidebar_items
        if (!$sidebarItem) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // SECURITY: Block access if sidebar_item has no permission_id
        if (!$sidebarItem->permission_id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Get the permission_id from sidebar_item
        $permissionId = $sidebarItem->permission_id;

        // Check if user has permission
        $hasPermission = DB::table('role_permission')
            ->where('user_id', $user->id)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$hasPermission) {
            // Return 403 Forbidden
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }

    /**
     * Get parent route from API route
     *
     * @param string $route
     * @return string|null
     */
    private function getParentRoute(string $route): ?string
    {
        // Specific route mappings (checked first, before prefix mappings)
        $exactRouteMap = [
            'users.show' => 'users.show', // Settings page - uses its own permission (manage_settings)
        ];

        // Check exact route mapping first
        if (isset($exactRouteMap[$route])) {
            return $exactRouteMap[$route];
        }

        // Map of route prefixes to their index routes
        $routeMap = [
            'bookings.' => 'bookings.index',
            'pendings.' => 'pendings.index',
            'newReserv.' => 'newReserv.index',
            'completed.' => 'completed.index',
            'checkin.' => 'checkin.index',
            'checkout.' => 'checkout.index',
            'changerooom.' => 'changerooom.index',
            'changeroom.' => 'changerooom.index',
            'room-availability.' => 'room-availability.index',
            'properties.' => 'properties.index',
            'rooms.' => 'rooms.index',
            'facilityProperty.' => 'facilityProperty.index',
            'facilityRooms.' => 'facilityRooms.index',
            'door-locks.' => 'door-locks.index',
            'admin.payments.' => 'admin.payments.index',
            'admin.refunds.' => 'admin.refunds.index',
            'admin.bookings.' => 'bookings.index',
            'customers.' => 'customers.index',
            'reports.booking.' => 'reports.booking.index',
            'reports.payment.' => 'reports.payment.index',
            'reports.rented-rooms.' => 'reports.rented-rooms.index',
            'vouchers.' => 'vouchers.index',
            'users-' => 'users-management',
            'users.' => 'users-management',
            'menus.' => 'users-management',
            'account.' => 'dashboard',
            'user-access.' => 'users-management',
            'master-role.' => 'master-role-management',
            'role.' => 'master-role-management',
            'dashboard-widgets.' => 'dashboard',
            'chat.' => 'chat.index',
        ];

        // Check each prefix
        foreach ($routeMap as $prefix => $indexRoute) {
            if (str_starts_with($route, $prefix)) {
                return $indexRoute;
            }
        }

        return null;
    }
}
