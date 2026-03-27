<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountSettings\UserSettingsController;
use App\Http\Controllers\Bookings\Booking\AllBookingController;
use App\Http\Controllers\Bookings\CheckIn\CheckInController;
use App\Http\Controllers\Bookings\CheckOut\CheckOutController;
use App\Http\Controllers\Bookings\Completed\CompletedController;
use App\Http\Controllers\Bookings\NewReservation\NewReservController;
use App\Http\Controllers\Bookings\Pending\PendingController;
use App\Http\Controllers\Rooms\ChangeRoomController;
use App\Http\Controllers\Properties\CalendarDateController;
use App\Http\Controllers\Properties\ManajementPropertiesController;
use App\Http\Controllers\Properties\ManajementRoomsController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\ParkingPaymentController;
use App\Http\Controllers\Payment\DepositPaymentController;
use App\Http\Controllers\Payment\RefundController;
use App\Http\Controllers\Properties\DepositFeeController;
use App\Http\Controllers\Properties\ParkingFeeController;
use App\Http\Controllers\Properties\PropertyFeesController;
use App\Http\Controllers\Properties\ParkingController;
use App\Http\Controllers\Properties\DoorLockController;
use App\Http\Controllers\RoomAvailability\RoomAvailabilityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Reports\BookingReportController;
use App\Http\Controllers\Reports\PaymentReportController;
use App\Http\Controllers\Reports\ParkingReportController;
use App\Http\Controllers\Reports\DepositReportController;
use App\Http\Controllers\Reports\RentedRoomsReportController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PromoBannerController;
use App\Http\Controllers\Chat\ChatController;
use Symfony\Component\Console\Command\CompleteCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

// Route::redirect('/', 'login');

Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $request->validate(['locale' => 'required|in:en,id']);
    session(['locale' => $request->locale]);
    return redirect()->back();
})->name('locale.update');

Route::middleware(['guest'])->group(function () {
    Route::get('/', [SessionController::class, 'index'])->name('login');
    Route::post('/', [SessionController::class, 'login']);
});
Route::get('/home', function () {
    return redirect('/dashboard');
});

Route::get('storage/{path}', function ($path) {
    // Remove any URL parameters if present
    $path = explode('?', $path)[0];

    // Build the full file path
    $filePath = storage_path('app/public/' . ltrim($path, '/'));

    if (!File::exists($filePath)) {
        abort(404, 'File not found');
    }

    try {
        $file = File::get($filePath);
        $type = File::mimeType($filePath);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        $response->header("Cache-Control", "public, max-age=31536000, immutable");

        return $response;
    } catch (\Exception $e) {
        abort(500, 'Error serving file');
    }
})->where('path', '.*');

Route::middleware(['auth', 'permission'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/room-report', [DashboardController::class, 'getPropertyRoomReport']);
    Route::get('/dashboard/room-report/{propertyId}', [DashboardController::class, 'getPropertyRoomReport']);
    Route::get('/dashboard/payment-methods', [DashboardController::class, 'getPaymentMethods']);
    Route::get('/dashboard/property-revenue', [DashboardController::class, 'getPropertyRevenue']);
    Route::get('/dashboard/property-revenue/{propertyId}', [DashboardController::class, 'getPropertyRevenue']);
    Route::get('/dashboard/revenue-trend', [DashboardController::class, 'getRevenueTrend']);
    Route::get('/dashboard/revenue-trend/{propertyId}', [DashboardController::class, 'getRevenueTrend']);
    Route::get('/progress', [DashboardController::class, 'progress_index'])->name('progress');

    Route::get('/account/getData', [UserController::class, 'accountGetData'])->name('account.getData');
    Route::get('/users/getList', [UserController::class, 'userGetData'])->name('users.getList');
    Route::get('/users/getMainMenus', [UserController::class, 'usergetMainMenus'])->name('menus.getMainMenus');

    Route::get('/menus/getData', [UserController::class, 'getData'])->name('menus.getData');
    Route::get('/menus/getUserAccess', [UserController::class, 'getUserAccess'])->name('menus.getUserAccess');

    Route::get('/settings/users-management', [UserController::class, 'index'])->name('users-management');
    Route::get('/settings/users-management/show', [UserController::class, 'show'])->name('users.show');

    // <!-- Maintenance mode routes: dedicated page + toggle/status API endpoints -->
    Route::get('/settings/maintenance', [\App\Http\Controllers\MaintenanceModeController::class, 'index'])->name('maintenance.index');
    Route::get('/settings/maintenance/status', [\App\Http\Controllers\MaintenanceModeController::class, 'status'])->name('maintenance.status');
    Route::post('/settings/maintenance/toggle', [\App\Http\Controllers\MaintenanceModeController::class, 'toggle'])->name('maintenance.toggle');

    Route::get('/settings/users-management/new', [UserController::class, 'indexNew'])->name('users-newManagement');
    Route::post('/settings/users-management/search', [UserController::class, 'searchUsers'])->name('users.search');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/settings/users-management/new', [UserController::class, 'store'])->name('users.store');

    Route::get('/settings/users-management/edit', [UserController::class, 'indexEdit'])->name('users-editManagement');
    Route::put('/settings/users/{user}', [UserController::class, 'updateUsers'])->name('users.update');
    Route::put('/settings/users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::put('/settings/users/{id}/reset-password', [UserController::class, 'resetUserPassword'])->name('users.resetPassword');

    Route::get('/settings/users-management/delete', [UserController::class, 'indexDelete'])->name('users-deleteManagement');
    Route::post('/settings/users/{user}/deactivate', [UserController::class, 'deactivateUser'])->name('users.deactivate');


    Route::get('/settings/users-access-management', [UserController::class, 'indexUserAccessManagement'])->name('users-access-management');
    Route::get('/user-access/edit', [UserController::class, 'indexUserAccessManagement'])->name('user-access.edit');
    Route::get('/user-access/{userId}/permissions', [UserController::class, 'getUserPermissions']);
    Route::post('/user-access/{userId}/update', [UserController::class, 'update']);

    // Master Role Management Routes
    Route::get('/settings/master-role-management', [UserController::class, 'indexMasterRole'])->name('master-role-management');
    Route::post('/master-role/search', [UserController::class, 'searchMasterRoleUsers'])->name('master-role.search');
    Route::post('/master-role/create', [UserController::class, 'createRole'])->name('master-role.create');
    Route::post('/master-role/update/{userId}', [UserController::class, 'updateMasterRole'])->name('master-role.update');
    Route::get('/master-role/permissions/{userId}', [UserController::class, 'getMasterRolePermissions'])->name('master-role.permissions');
    Route::post('/master-role/update-permissions/{userId}', [UserController::class, 'updateMasterRolePermissions'])->name('master-role.update-permissions');

    // Dashboard Widget Management Routes
    Route::get('/dashboard-widgets', [UserController::class, 'getDashboardWidgets'])->name('dashboard-widgets.get');
    Route::get('/role/{roleId}/dashboard-widgets', [UserController::class, 'getRoleDashboardWidgets'])->name('role.dashboard-widgets.get');
    Route::post('/role/{roleId}/dashboard-widgets', [UserController::class, 'updateRoleDashboardWidgets'])->name('role.dashboard-widgets.update');

    // User Settings Routes
    Route::put('/user/password', [UserSettingsController::class, 'updatePassword'])->name('user.password.update');
    Route::put('/user/profile', [UserSettingsController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/user/activity', [UserSettingsController::class, 'getUserActivity'])->name('user.activity');
    Route::post('/user/locale', [UserSettingsController::class, 'updateLocale'])->name('user.locale.update');

    Route::prefix('bookings')->group(function () {
        Route::get('/bookings', [AllBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/filter', [AllBookingController::class, 'filter'])->name('bookings.filter');

        Route::get('/pendings', [PendingController::class, 'index'])->name('pendings.index');
        Route::get('/pendings/filter', [PendingController::class, 'filter'])->name('pendings.filter');

        Route::get('/newReserv', [NewReservController::class, 'index'])->name('newReserv.index');
        Route::get('/newReserv/filter', [NewReservController::class, 'filter'])->name('newReserv.filter');
        Route::post('/newReserv/{order_id}', [NewReservController::class, 'checkIn'])->name('newReserv.checkin');
        Route::get('/newReserv-in/{order_id}/details', [NewReservController::class, 'getBookingDetails'])->name('newReserv.checkin.details');

        Route::get('/newReserv-in/{order_id}/regist', [NewReservController::class, 'getRegist'])->name('newReserv.checkin.regist');
        Route::get('/newReserv-in/{order_id}/invoice', [NewReservController::class, 'getInvoice'])->name('newReserv.checkin.invoice');

        Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
        Route::get('/checkin/filter', [CheckInController::class, 'filter'])->name('checkin.filter');
        Route::post('/checkin/{order_id}', [CheckInController::class, 'checkIn'])->name('bookings.checkin');
        Route::get('/check-in/{order_id}/details', [CheckInController::class, 'getBookingDetails'])->name('bookings.checkin.details');
        
        Route::get('/checkout', [CheckOutController::class, 'index'])->name('checkout.index');
        Route::get('/checkout/filter', [CheckOutController::class, 'filter'])->name('checkout.filter');
        Route::post('/check-out/{order_id}', [CheckOutController::class, 'checkOut'])->name('bookings.checkout');
        Route::get('/check-out/{order_id}/details', [CheckOutController::class, 'getBookingDetails'])->name('bookings.checkin.details');

        Route::get('/completed', [CompletedController::class, 'index'])->name('completed.index');
        Route::get('/completed/filter', [CompletedController::class, 'filter'])->name('completed.filter');
    });
    
    // ---------------------------------------------------------------------------------------------------------------------
    Route::prefix('rooms')->group(function () {
        Route::get('/change-room', [ChangeRoomController::class, 'index'])->name('changerooom.index');
        Route::get('/change-room/available-rooms', [ChangeRoomController::class, 'getAvailableRooms']);
        Route::post('/change-room/store', [ChangeRoomController::class, 'store'])->name('changeroom.store');
        Route::post('/change-room/rollback', [ChangeRoomController::class, 'rollback'])->name('changeroom.rollback');
        Route::get('/change-room/chain', [ChangeRoomController::class, 'getChain'])->name('changeroom.chain');
        Route::get('/change-room/check-rollback', [ChangeRoomController::class, 'checkRollbackAvailability'])->name('changeroom.check-rollback');

        // ---------------------------------------------------------------------------------------------------------------------

        Route::get('/room-availability', [RoomAvailabilityController::class, 'index'])->name('room-availability.index');
        Route::get('/room-availability/data', [RoomAvailabilityController::class, 'getAvailabilityData'])->name('room-availability.data');
        Route::post('/room-availability/{id}/status', [RoomAvailabilityController::class, 'updateRentalStatus'])->name('room-availability.update-status');
        Route::get('/room-availability/{room}/bookings', [RoomAvailabilityController::class, 'getRoomBookings'])->name('room-availability.bookings');
    });

    Route::prefix('properties')->group(function () {
        Route::get('/m-properties', [ManajementPropertiesController::class, 'index'])->name('properties.index');
        Route::post('/m-properties/filter', [ManajementPropertiesController::class, 'filter'])->name('properties.filter');
        Route::post('/properties/toggle-status', [ManajementPropertiesController::class, 'toggleStatus'])->name('properties.toggle-status');

        Route::put('/m-properties/{property}/status', [ManajementPropertiesController::class, 'updateStatus'])->name('properties.updateStatus');
        Route::post('/m-properties/set-active-all', [ManajementPropertiesController::class, 'setActiveAll'])->name('properties.setActiveAll');
        Route::post('/m-properties/store', [ManajementPropertiesController::class, 'store'])->name('properties.store');
        Route::put('/m-properties/update/{idrec}', [ManajementPropertiesController::class, 'update'])->name('properties.update');
        Route::get('/m-properties/table', [ManajementPropertiesController::class, 'tablePartial'])->name('properties.table');

        Route::get('/m-properties/facility', [ManajementPropertiesController::class, 'indexFacility'])->name('facilityProperty.index');
        Route::post('/m-properties/facility/store', [ManajementPropertiesController::class, 'storeFacility'])->name('facilityProperty.store');
        Route::put('/m-properties/facility/update/{id}', [ManajementPropertiesController::class, 'updateFacility'])->name('facilityProperty.update');
        Route::post('/m-properties/facility/toggle-status', [ManajementPropertiesController::class, 'toggleFacilityStatus'])->name('facilityProperty.toggle-status');

        // ------------------------- CITIES MANAGEMENT -------------------------
        Route::get('/m-properties/cities', [ManajementPropertiesController::class, 'indexCity'])->name('cityProperty.index');
        Route::post('/m-properties/cities/store', [ManajementPropertiesController::class, 'storeCity'])->name('cityProperty.store');
        Route::put('/m-properties/cities/update/{id}', [ManajementPropertiesController::class, 'updateCity'])->name('cityProperty.update');
        Route::post('/m-properties/cities/toggle-status', [ManajementPropertiesController::class, 'toggleCityStatus'])->name('cityProperty.toggle-status');

        // ------------------------- GLOBAL CALENDAR MANAGEMENT -------------------------
        Route::get('/calendar', [CalendarDateController::class, 'index'])->name('calendar.index');
        Route::get('/calendar/entries', [CalendarDateController::class, 'getEntries'])->name('calendar.entries');
        Route::post('/calendar/store', [CalendarDateController::class, 'storeRange'])->name('calendar.store');
        Route::post('/calendar/toggle-status', [CalendarDateController::class, 'toggleStatus'])->name('calendar.toggle-status');
        Route::post('/calendar/regenerate-all', [CalendarDateController::class, 'regenerateAll'])->name('calendar.regenerate-all');

        // ------------------------- ROOMS MANAGEMENT -------------------------
        Route::get('/m-rooms', [ManajementRoomsController::class, 'index'])->name('rooms.index');
        Route::post('/rooms/store', [ManajementRoomsController::class, 'store'])->name('rooms.store');
        Route::post('/rooms/check-room-number', [ManajementRoomsController::class, 'checkRoomNumber'])->name('rooms.check-room-number');
        Route::put('/rooms/update/{idrec}', [ManajementRoomsController::class, 'update'])->name('rooms.update');
        Route::put('/rooms/{room}/status', [ManajementRoomsController::class, 'updateStatus'])->name('room.updateStatus');
        Route::post('/rooms/set-active-all', [ManajementRoomsController::class, 'setActiveAll'])->name('rooms.setActiveAll');
        Route::get('/rooms/{id}', [ManajementRoomsController::class, 'show'])->where('id', '[0-9]+')->name('rooms.show');
        Route::get('/rooms/table', [ManajementRoomsController::class, 'tablePartial'])->name('properties.table');
        Route::delete('/rooms/{idrec}/destroy', [ManajementRoomsController::class, 'destroy'])->name('rooms.destoy');
        Route::get('/rooms/{room}/edit-prices', [ManajementRoomsController::class, 'changePriceIndex'])->name('rooms.prices.change-price-index');
        Route::get('/rooms/{room}/price', [ManajementRoomsController::class, 'getPriceForDate'])->name('rooms.prices.date');
        Route::post('/rooms/{room}/update-price', [ManajementRoomsController::class, 'updatePriceRange'])->name('rooms.prices.update');
        Route::get('/rooms/{room}/prices', [ManajementRoomsController::class, 'getRoomPrices'])->name('rooms.prices.index');

        // <!-- Multi-Tier Pricing: Pricing rules CRUD routes -->
        Route::get('/rooms/{room}/pricing-rules', [\App\Http\Controllers\Properties\RoomPricingRulesController::class, 'index'])->name('rooms.pricing-rules.index');
        Route::post('/rooms/{room}/pricing-rules', [\App\Http\Controllers\Properties\RoomPricingRulesController::class, 'store'])->name('rooms.pricing-rules.store');
        Route::put('/rooms/{room}/pricing-rules/{rule}', [\App\Http\Controllers\Properties\RoomPricingRulesController::class, 'update'])->name('rooms.pricing-rules.update');
        Route::delete('/rooms/{room}/pricing-rules/{rule}', [\App\Http\Controllers\Properties\RoomPricingRulesController::class, 'destroy'])->name('rooms.pricing-rules.destroy');
        Route::post('/rooms/{room}/regenerate-prices', [\App\Http\Controllers\Properties\RoomPricingRulesController::class, 'regenerate'])->name('rooms.pricing-rules.regenerate');

        Route::get('/m-rooms/facilityRooms', [ManajementRoomsController::class, 'indexFacility'])->name('facilityRooms.index');
        Route::post('/rooms/facilityRooms/store', [ManajementRoomsController::class, 'storeFacility'])->name('facilityRooms.store');
        Route::put('/rooms/facilityRooms/update/{id}', [ManajementRoomsController::class, 'updateFacility'])->name('facilityRooms.update');
        Route::post('/rooms/facilityRooms/toggle-status', [ManajementRoomsController::class, 'toggleFacilityStatus'])->name('facilityRooms.toggle-status');

        // ------------------------- ROOM NAME TYPE MANAGEMENT -------------------------
        Route::get('/rooms/room-name-types', [ManajementRoomsController::class, 'indexRoomNameType'])->name('roomNameTypes.index');
        Route::post('/rooms/room-name-types/store', [ManajementRoomsController::class, 'storeRoomNameType'])->name('roomNameTypes.store');
        Route::put('/rooms/room-name-types/update/{id}', [ManajementRoomsController::class, 'updateRoomNameType'])->name('roomNameTypes.update');
        Route::post('/rooms/room-name-types/toggle-status', [ManajementRoomsController::class, 'toggleRoomNameTypeStatus'])->name('roomNameTypes.toggle-status');

        // ------------------------- DOOR LOCK MANAGEMENT -------------------------
        Route::get('/rooms/door-locks', [DoorLockController::class, 'index'])->name('door-locks.index');
        Route::post('/rooms/door-locks/get-details', [DoorLockController::class, 'getLockDetails'])->name('door-locks.get-details');
        Route::post('/rooms/door-locks/store', [DoorLockController::class, 'store'])->name('door-locks.store');
        Route::post('/rooms/door-locks/{id}/passcode', [DoorLockController::class, 'addPasscode'])->name('door-locks.passcode');
        Route::delete('/rooms/door-locks/{id}', [DoorLockController::class, 'destroy'])->name('door-locks.destroy');

        // ------------------------- UNIFIED DEPOSIT & PARKING FEE PAGE -------------------------
        Route::get('/property-fees', [PropertyFeesController::class, 'index'])->name('property-fees.index');
        Route::post('/property-fees/filter', [PropertyFeesController::class, 'filter'])->name('property-fees.filter');
        Route::post('/property-fees/store-or-update', [PropertyFeesController::class, 'storeOrUpdate'])->name('property-fees.store-or-update');

        // ------------------------- DEPOSIT FEE MANAGEMENT -------------------------
        Route::get('/deposit-fees', [DepositFeeController::class, 'index'])->name('deposit-fees.index');
        Route::post('/deposit-fees/filter', [DepositFeeController::class, 'filter'])->name('deposit-fees.filter');
        Route::post('/deposit-fees/store', [DepositFeeController::class, 'store'])->name('deposit-fees.store');
        Route::put('/deposit-fees/update/{idrec}', [DepositFeeController::class, 'update'])->name('deposit-fees.update');
        Route::post('/deposit-fees/toggle-status', [DepositFeeController::class, 'toggleStatus'])->name('deposit-fees.toggle-status');

        // ------------------------- PARKING FEE MANAGEMENT -------------------------
        Route::get('/parking-fees', [ParkingFeeController::class, 'index'])->name('parking-fees.index');
        Route::post('/parking-fees/filter', [ParkingFeeController::class, 'filter'])->name('parking-fees.filter');
        Route::post('/parking-fees/store', [ParkingFeeController::class, 'store'])->name('parking-fees.store');
        Route::put('/parking-fees/update/{idrec}', [ParkingFeeController::class, 'update'])->name('parking-fees.update');
        Route::post('/parking-fees/toggle-status', [ParkingFeeController::class, 'toggleStatus'])->name('parking-fees.toggle-status');
        Route::delete('/parking-fees/destroy/{idrec}', [ParkingFeeController::class, 'destroy'])->name('parking-fees.destroy');
        Route::post('/parking-fees/restore/{idrec}', [ParkingFeeController::class, 'restore'])->name('parking-fees.restore');
        Route::post('/parking-fees/adjust-quota/{idrec}', [ParkingFeeController::class, 'adjustQuota'])->name('parking-fees.adjust-quota');

        // ------------------------- PARKING MANAGEMENT -------------------------
        Route::get('/parking', [ParkingController::class, 'index'])->name('parking.index');
        Route::post('/parking/store', [ParkingController::class, 'store'])->name('parking.store');
        Route::post('/parking/filter', [ParkingController::class, 'filter'])->name('parking.filter');
        Route::put('/parking/update/{idrec}', [ParkingController::class, 'update'])->name('parking.update');
        Route::post('/parking/toggle-status', [ParkingController::class, 'toggleStatus'])->name('parking.toggle-status');
        Route::delete('/parking/destroy/{idrec}', [ParkingController::class, 'destroy'])->name('parking.destroy');
        Route::post('/parking/restore/{idrec}', [ParkingController::class, 'restore'])->name('parking.restore');
    });

    Route::prefix('payment')->group(function () {
        Route::get('/pay', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('/payments/filter', [PaymentController::class, 'filter'])->name('admin.payments.filter');
        Route::post('/approve/{id}', [PaymentController::class, 'approve'])->name('admin.payments.approve');
        Route::post('/reject/{id}', [PaymentController::class, 'reject'])->name('admin.payments.reject');
        Route::put('/cancel/{id}', [PaymentController::class, 'cancel'])->name('admin.bookings.cancel');
        Route::put('/update-payment-date/{id}', [PaymentController::class, 'updatePaymentDate'])->name('admin.payments.update-payment-date');
        Route::put('/update-checkinout/{id}', [PaymentController::class, 'updateCheckInOut'])->name('admin.payments.update-checkinout');
        Route::post('/update-notes/{id}', [PaymentController::class, 'updateNotes'])->name('admin.payments.update-notes');

        // ------------------------- PARKING PAYMENTS -------------------------
        Route::get('/parking', [ParkingPaymentController::class, 'index'])->name('admin.parking-payments.index');
        /* Accept both GET (pagination links) and POST (AJAX filter) */
        Route::match(['get', 'post'], '/parking/filter', [ParkingPaymentController::class, 'filter'])->name('admin.parking-payments.filter');
        Route::get('/parking/checked-in-orders', [ParkingPaymentController::class, 'getCheckedInOrders'])->name('admin.parking-payments.checked-in-orders');
        Route::post('/parking/store', [ParkingPaymentController::class, 'store'])->name('admin.parking-payments.store');
        Route::post('/parking/approve/{id}', [ParkingPaymentController::class, 'approve'])->name('admin.parking-payments.approve');
        Route::post('/parking/reject/{id}', [ParkingPaymentController::class, 'reject'])->name('admin.parking-payments.reject');
        Route::post('/parking/checkout/{id}', [ParkingPaymentController::class, 'checkout'])->name('admin.parking-payments.checkout');
        Route::get('/parking/proof/{id}', [ParkingPaymentController::class, 'viewProof'])->name('admin.parking-payments.proof');

        // ------------------------- DEPOSIT PAYMENTS -------------------------
        Route::get('/deposit', [DepositPaymentController::class, 'index'])->name('admin.deposit-payments.index');
        /* Accept GET (pagination links) and POST (AJAX filter) */
        Route::match(['get', 'post'], '/deposit/filter', [DepositPaymentController::class, 'filter'])->name('admin.deposit-payments.filter');
        Route::get('/deposit/checked-in-orders', [DepositPaymentController::class, 'getCheckedInOrders'])->name('admin.deposit-payments.checked-in-orders');
        Route::post('/deposit/store', [DepositPaymentController::class, 'store'])->name('admin.deposit-payments.store');
        Route::post('/deposit/approve/{id}', [DepositPaymentController::class, 'approve'])->name('admin.deposit-payments.approve');
        Route::post('/deposit/reject/{id}', [DepositPaymentController::class, 'reject'])->name('admin.deposit-payments.reject');
        Route::get('/deposit/proof/{id}', [DepositPaymentController::class, 'viewProof'])->name('admin.deposit-payments.proof');

        Route::get('/refund', [RefundController::class, 'index'])->name('admin.refunds.index');
        Route::post('/refund/store', [RefundController::class, 'store'])->name('admin.refunds.store');
        Route::post('/refund/cancel/{id_booking}', [RefundController::class, 'cancel'])->name('admin.refunds.cancel');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/filter', [CustomerController::class, 'filter'])->name('customers.filter');
        Route::post('/pre-register', [CustomerController::class, 'preRegister'])->name('customers.pre-register');
        Route::put('/{id}/update', [CustomerController::class, 'updateCustomer'])->name('customers.update');
        Route::get('/{identifier}/bookings', [CustomerController::class, 'getBookings'])->name('customers.bookings');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/booking-report', [BookingReportController::class, 'index'])->name('reports.booking.index');
        Route::get('/booking-report/data', [BookingReportController::class, 'getData'])->name('reports.booking.data');
        Route::get('/booking-report/export', [BookingReportController::class, 'export'])->name('reports.booking.export');

        Route::get('/payment-report', [PaymentReportController::class, 'index'])->name('reports.payment.index');
        Route::get('/payment-report/data', [PaymentReportController::class, 'getData'])->name('reports.payment.data');
        Route::get('/payment-report/export', [PaymentReportController::class, 'export'])->name('reports.payment.export');

        Route::get('/parking-report', [ParkingReportController::class, 'index'])->name('reports.parking.index');
        Route::get('/parking-report/data', [ParkingReportController::class, 'getData'])->name('reports.parking.data');
        Route::get('/parking-report/export', [ParkingReportController::class, 'export'])->name('reports.parking.export');

        Route::get('/deposit-report', [DepositReportController::class, 'index'])->name('reports.deposit.index');
        Route::get('/deposit-report/data', [DepositReportController::class, 'getData'])->name('reports.deposit.data');
        Route::get('/deposit-report/export', [DepositReportController::class, 'export'])->name('reports.deposit.export');

        Route::get('/rented-rooms-report', [RentedRoomsReportController::class, 'index'])->name('reports.rented-rooms.index');
        Route::get('/rented-rooms-report/data', [RentedRoomsReportController::class, 'getData'])->name('reports.rented-rooms.data');
        Route::get('/rented-rooms-report/export', [RentedRoomsReportController::class, 'export'])->name('reports.rented-rooms.export');
    });

    Route::prefix('vouchers')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('vouchers.index');
        Route::get('/filter', [VoucherController::class, 'filter'])->name('vouchers.filter');
        Route::post('/toggle-status', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status');
        Route::post('/store', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::get('/{id}', [VoucherController::class, 'show'])->where('id', '[0-9]+')->name('vouchers.show');
        Route::put('/{id}', [VoucherController::class, 'update'])->where('id', '[0-9]+')->name('vouchers.update');
        Route::delete('/{id}', [VoucherController::class, 'destroy'])->where('id', '[0-9]+')->name('vouchers.destroy');
    });

    Route::prefix('promo-banners')->group(function () {
        Route::get('/', [PromoBannerController::class, 'index'])->name('promo-banners.index');
        Route::get('/filter', [PromoBannerController::class, 'filter'])->name('promo-banners.filter');
        Route::post('/toggle-status', [PromoBannerController::class, 'toggleStatus'])->name('promo-banners.toggle-status');
        Route::post('/store', [PromoBannerController::class, 'store'])->name('promo-banners.store');
        Route::get('/{id}', [PromoBannerController::class, 'show'])->where('id', '[0-9]+')->name('promo-banners.show');
        Route::put('/{id}', [PromoBannerController::class, 'update'])->where('id', '[0-9]+')->name('promo-banners.update');
        Route::delete('/{id}', [PromoBannerController::class, 'destroy'])->where('id', '[0-9]+')->name('promo-banners.destroy');
    });

    /** Ticket Management Routes — customer service ticketing system */
    Route::prefix('tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tickets\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/filter', [\App\Http\Controllers\Tickets\TicketController::class, 'filter'])->name('tickets.filter');
        Route::get('/unread-count', [\App\Http\Controllers\Tickets\TicketController::class, 'getUnreadCount'])->name('tickets.unread-count');
        Route::get('/{id}', [\App\Http\Controllers\Tickets\TicketController::class, 'show'])->where('id', '[0-9]+')->name('tickets.show');
        Route::post('/{ticketId}/send', [\App\Http\Controllers\Tickets\TicketController::class, 'sendMessage'])->name('tickets.send');
        Route::post('/{ticketId}/upload-image', [\App\Http\Controllers\Tickets\TicketController::class, 'uploadImage'])->name('tickets.upload-image');
        Route::post('/{ticketId}/close', [\App\Http\Controllers\Tickets\TicketController::class, 'closeTicket'])->name('tickets.close');
        Route::post('/{ticketId}/reopen', [\App\Http\Controllers\Tickets\TicketController::class, 'reopenTicket'])->name('tickets.reopen');
        Route::post('/{ticketId}/status', [\App\Http\Controllers\Tickets\TicketController::class, 'updateStatus'])->name('tickets.status');

        /** Broadcast Routes — one-way announcements to users */
        Route::get('/broadcasts', [\App\Http\Controllers\Tickets\BroadcastController::class, 'index'])->name('broadcasts.index');
        Route::get('/broadcasts/create', [\App\Http\Controllers\Tickets\BroadcastController::class, 'create'])->name('broadcasts.create');
        Route::post('/broadcasts/store', [\App\Http\Controllers\Tickets\BroadcastController::class, 'store'])->name('broadcasts.store');
        Route::get('/broadcasts/{id}', [\App\Http\Controllers\Tickets\BroadcastController::class, 'show'])->where('id', '[0-9]+')->name('broadcasts.show');
    });

    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/filter', [ChatController::class, 'filter'])->name('chat.filter');
        Route::get('/find-by-order', [ChatController::class, 'findByOrder'])->name('chat.find-by-order');
        Route::get('/conversations-json', [ChatController::class, 'getConversationsJson'])->name('chat.conversations-json');
        Route::get('/{id}', [ChatController::class, 'show'])->where('id', '[0-9]+')->name('chat.show');
        Route::post('/store', [ChatController::class, 'store'])->name('chat.store');
        Route::post('/{conversationId}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/checked-in-users', [ChatController::class, 'getCheckedInUsers'])->name('chat.checked-in-users');
        Route::post('/{conversationId}/upload-image', [ChatController::class, 'uploadImage'])->name('chat.upload-image');
        Route::put('/messages/{id}/edit', [ChatController::class, 'editMessage'])->name('chat.messages.edit');
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    });
});
