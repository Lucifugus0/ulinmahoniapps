<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\DokuServiceController as DokuController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\VoucherAdminController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PromoBannerController;
use App\Http\Controllers\Api\DepositFeeController;
use App\Http\Controllers\Api\ParkingFeeController;
use App\Http\Controllers\Api\DeviceTokenController;

use App\Http\Middleware\VerifyApiKey;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (no API key required)
Route::get('health-check', [HealthCheckController::class, 'check']);


// API routes that require a valid API key in the X-API-KEY header
// Protected routes that require both API key and authentication



// ===== V1 API ROUTES =====
// Version 1 API routes (require API key but not necessarily authentication)

Route::prefix('v1')->group(function () {
    
    // AUTH ROUTES WITHOUT PREFIX AUTH
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register-without-verification', [AuthController::class, 'registerWithoutVerification']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('resend-verification', [AuthController::class, 'resendVerification']);
    
    // AUTH API ROUTES
    Route::prefix('auth')->group(function () {
        // GOOGLE ( UNUSED, COMMENTED FOR REVIEW )
        // Route::get('/google', [SocialAuthController::class, 'redirectToGoogle']);
        // Route::post('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
        // Route::middleware('auth:sanctum')->post('/logout', [SocialAuthController::class, 'logout']);

        // Public authentication endpoints
        Route::post('register', [AuthController::class, 'register']);
        Route::post('register-without-verification', [AuthController::class, 'registerWithoutVerification']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('resend-verification', [AuthController::class, 'resendVerification']);
    });

    // ROUTES THAT REQUIRE API KEY (MIDDLEWARE)
    Route::middleware([VerifyApiKey::class])->group(function () {

        // BANNER API ROUTES
        Route::prefix('banner')->group(function () {
            Route::get('/', [BannerController::class, 'index']);
            Route::get('/{id}', [BannerController::class, 'show']);
            Route::post('/', [BannerController::class, 'store']);
            Route::put('/{id}', [BannerController::class, 'update']);
            Route::delete('/{id}', [BannerController::class, 'destroy']);
        });

        // PROPERTY API ROUTES
        Route::prefix('property')->group(function () {
            Route::get('/', [PropertyController::class, 'index']);
            Route::get('/{id}', [PropertyController::class, 'show']);
            Route::post('/', [PropertyController::class, 'store']);
            Route::put('/{id}', [PropertyController::class, 'update']);
            Route::delete('/{id}', [PropertyController::class, 'destroy']);
        });

        // BOOKING API ROUTES
        Route::prefix('booking')->group(function () {
            Route::get('/', [BookingController::class, 'index']);
            Route::get('/{id}', [BookingController::class, 'show']);
            Route::get('/order/{order_id}', [BookingController::class, 'showByOrderId']);
            Route::get('/userId/{user_id}', [BookingController::class, 'showByUserId']);
            Route::post('/', [BookingController::class, 'store']);
            Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.check-availability');
            Route::put('/{id}', [BookingController::class, 'update']);
            Route::post('/{id}/upload', [BookingController::class, 'uploadAttachment']);
            Route::put('/{id}/update-attachment', [BookingController::class, 'updateAttachment']);
            Route::put('/{id}/payment-method', [BookingController::class, 'updatePaymentMethod']);
            Route::post('/{order_id}/check-in', [BookingController::class, 'checkInByOrderId']);
            Route::post('/{order_id}/renew', [BookingController::class, 'renewBooking']);
            // Removed duplicate check-availability route
        });

        // DOKU PAYMENT API ROUTES
        Route::prefix('doku')->group(function () {
            Route::post('/process-payment', [BookingController::class, 'processDokuPaymentEndpoint']);
            Route::get('/test-token-b2b', [BookingController::class, 'testDokuGetTokenB2B']);
            Route::get('/available-banks', [BookingController::class, 'getDokuAvailableBanks']);
            Route::post('/test-generate-va', [BookingController::class, 'testDokuGenerateVA']);
            Route::post('/test-generate-qris', [BookingController::class, 'testDokuGenerateQris']);
            Route::post('/test-generate-cc', [BookingController::class, 'testDokuGenerateCC']);
        });

        // USER API ROUTES
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
            Route::post('/{id}/deactivate', [UserController::class, 'deactivate']);
        });

        // ROOM API ROUTES
        Route::prefix('rooms')->group(function () {
            Route::get('/', [RoomController::class, 'index']);
            Route::get('/propertyId/{property_id}', [RoomController::class, 'byPropertyId']);
            Route::get('/{id}', [RoomController::class, 'show']);
            Route::post('/', [RoomController::class, 'store']);
            Route::put('/{id}', [RoomController::class, 'update']);
            Route::delete('/{id}', [RoomController::class, 'destroy']);
            /* Multi-Tier Pricing: Price preview endpoint for daily booking price breakdown */
            Route::get('/{roomId}/price-preview', [RoomController::class, 'pricePreview']);
        });

        // PROFILE API ROUTES
        Route::prefix('profile')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/', [AuthController::class, 'profile']);
            Route::put('/{id}', [AuthController::class, 'updateProfile']);
            Route::put('/{id}/update-password', [AuthController::class, 'updatePassword']);
            Route::post('/{id}/profile-picture', [AuthController::class, 'updateProfilePicture']);
        });

        // VOUCHER API ROUTES (USER)
        Route::prefix('voucher')->group(function () {
            Route::post('/validate', [VoucherController::class, 'validateVoucher']);
            Route::post('/apply', [VoucherController::class, 'apply']);
            Route::get('/history/{user_id}', [VoucherController::class, 'getUserHistory']);
            Route::get('/available', [VoucherController::class, 'availableVouchers']);
        });

        // VOUCHER API ROUTES (ADMIN)
        Route::prefix('admin/voucher')->group(function () {
            Route::get('/', [VoucherAdminController::class, 'index']);
            Route::get('/{id}', [VoucherAdminController::class, 'show']);
            Route::post('/', [VoucherAdminController::class, 'store']);
            Route::put('/{id}', [VoucherAdminController::class, 'update']);
            Route::delete('/{id}', [VoucherAdminController::class, 'destroy']);
            Route::get('/{id}/usage-logs', [VoucherAdminController::class, 'usageLogs']);
            Route::post('/generate-code', [VoucherAdminController::class, 'generateCode']);
        });

        // SEARCH API ROUTES
        Route::prefix('search')->group(function () {
            Route::get('/rooms', [SearchController::class, 'searchRooms'])->name('api.search.rooms');
        });

        // CHAT API ROUTES
        Route::prefix('chat')->group(function () {
            // User routes
            Route::get('/conversations', [ChatController::class, 'listConversations']);
            Route::get('/conversations/{id}', [ChatController::class, 'getConversation']);
            Route::post('/conversations', [ChatController::class, 'createConversation']);
            Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage']);
            Route::post('/conversations/{id}/read', [ChatController::class, 'markAsRead']);
            Route::put('/messages/{id}', [ChatController::class, 'editMessage']);

            // Admin routes - these methods handle authorization internally
            Route::get('/admin/conversations', [ChatController::class, 'adminListConversations']);
            Route::post('/conversations/{id}/participants', [ChatController::class, 'assignParticipant']);
        });

        // PROMO BANNER API ROUTES
        // Use ?id= query param to get single banner
        Route::prefix('promo-banner')->group(function () {
            Route::get('/', [PromoBannerController::class, 'index']);
            Route::post('/', [PromoBannerController::class, 'store']);
            Route::put('/{id}', [PromoBannerController::class, 'update']);
            Route::delete('/{id}', [PromoBannerController::class, 'destroy']);
            Route::post('/{id}/images', [PromoBannerController::class, 'addImages']);
            Route::delete('/{id}/images/{imageId}', [PromoBannerController::class, 'removeImage']);
        });

        // DEPOSIT FEE API ROUTES
        Route::prefix('deposit-fee')->group(function () {
            Route::get('/', [DepositFeeController::class, 'index']);
            Route::get('/property/{property_id}', [DepositFeeController::class, 'byPropertyId']);
            Route::get('/{id}', [DepositFeeController::class, 'show']);
            Route::post('/', [DepositFeeController::class, 'store']);
            Route::put('/{id}', [DepositFeeController::class, 'update']);
            Route::delete('/{id}', [DepositFeeController::class, 'destroy']);
        });

        // PARKING FEE API ROUTES
        Route::prefix('parking-fee')->group(function () {
            Route::get('/', [ParkingFeeController::class, 'index']);
            Route::get('/property/{property_id}', [ParkingFeeController::class, 'byPropertyId']);
            Route::get('/{id}', [ParkingFeeController::class, 'show']);
            Route::post('/', [ParkingFeeController::class, 'store']);
            Route::put('/{id}', [ParkingFeeController::class, 'update']);
            Route::delete('/{id}', [ParkingFeeController::class, 'destroy']);
        });

        // DEVICE TOKEN API ROUTES (Push Notifications)
        Route::prefix('device-token')->group(function () {
            Route::post('/', [DeviceTokenController::class, 'store']);
            Route::delete('/', [DeviceTokenController::class, 'destroy']);
        });

        // PUSH NOTIFICATION API ROUTES (Manual send)
        Route::post('/push-notification/send', [NotificationController::class, 'sendPushNotification']);

        // COMMENTED FOR REVIEW
        // NOTIFICATIONS API ROUTES
        // Route::prefix('notifications')->group(function () {
        //     Route::get('/', [NotificationController::class, 'getNotifications']);
        //     Route::post('/payment', [NotificationController::class, 'paymentNotifications']);
        //     Route::post('/mark-as-read', [NotificationController::class, 'markNotificationsAsRead']);
        // });

        // COMMENTED FOR REVIEW
        // DOKU API ROUTES
        // Route::prefix('doku')->withoutMiddleware(['api.key'])->middleware('App\Http\Middleware\DokuHeaderMiddleware')->group(function () {
        //     Route::post('/transfer-va/payment', [NotificationController::class, 'dokuPaymentNotification']);
        //     Route::post('/qr-mpm/payment', [NotificationController::class, 'dokuQRPaymentNotification']); 
        // });        
    });
}); 
// END OF V1 API ROUTES
// END OF VerifyApiKey middleware group`

// DOKU API ROUTES ( DO NOT COMMENT THIS PART OF CODE )
// DOKU SNAP API
Route::prefix('')->middleware('App\Http\Middleware\DokuHeaderMiddleware')->group(function () {
    Route::post('v1/transfer-va/payment', [DokuController::class, 'dokuPaymentNotification']);
});

// DOKU NON SNAP API
Route::prefix('')->middleware('App\Http\Middleware\DokuNonSnapHeaderMiddleware')->group(function () {
    Route::post('v1/qr-mpm/payment', [DokuController::class, 'dokuQRPaymentNotification']); 
});
Route::prefix('')->middleware('App\Http\Middleware\DokuNonSnapHeaderMiddleware')->group(function () {
    Route::post('v1/credit-card/payment', [DokuController::class, 'dokuCreditCardPaymentNotification']); 
});

// DOKU B2B API
Route::prefix('')->middleware('App\Http\Middleware\DokuB2BHeaderMiddleware')->group(function () {
    Route::post('/authorization/v1/access-token/b2b', [DokuController::class, 'dokuGetTokenB2B']);
});
// END OF DOKU API ROUTES

// ===== LEGACY API ROUTES =====
// Legacy API routes (if needed for backward compatibility)
// These will be accessible without the v1 prefix

// SOME OF THE CODES BELOWS ARE COMMENTED FOR REVIEW REASONS
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('resend-verification', [AuthController::class, 'resendVerification']);


// AUTH API ROUTES 
// Route::prefix('auth')->group(function () {
//     // GOOGLE ( UNUSED, COMMENTED FOR REVIEW )
//     // Route::get('/google', [SocialAuthController::class, 'redirectToGoogle']);
//     // Route::post('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
//     // Route::middleware('auth:sanctum')->post('/logout', [SocialAuthController::class, 'logout']);
    
//     // Public authentication endpoints
//     Route::post('register', [AuthController::class, 'register']);
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
//     Route::post('reset-password', [AuthController::class, 'resetPassword']);
// });

// BANNER API ROUTES
// Route::get('banner', [BannerController::class, 'index']);
// Route::get('banner/{id}', [BannerController::class, 'show']);
// Route::post('banner', [BannerController::class, 'store']);
// Route::put('banner/{id}', [BannerController::class, 'update']);
// Route::delete('banner/{id}', [BannerController::class, 'destroy']);

// // Property API - these routes will be accessible at /api/your-api-key/property
// Route::prefix('property')->group(function () {
//     Route::get('/', [PropertyController::class, 'index']);
//     Route::get('/{id}', [PropertyController::class, 'show']);
//     Route::post('/', [PropertyController::class, 'store']);
//     Route::put('/{id}', [PropertyController::class, 'update']);
//     Route::delete('/{id}', [PropertyController::class, 'destroy']);
// });

// // Booking API routes
// Route::prefix('booking')->group(function () {
//     Route::get('/', [BookingController::class, 'index']);
//     Route::get('/{id}', [BookingController::class, 'show']);
//     Route::get('/order/{order_id}', [BookingController::class, 'showByOrderId']);
//     Route::get('/userId/{user_id}', [BookingController::class, 'showByUserId']);
//     Route::post('/', [BookingController::class, 'store']);
//     Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.check-availability');
//     Route::put('/{id}', [BookingController::class, 'update']);
//     Route::post('/{id}/upload', [BookingController::class, 'uploadAttachment']);
//     Route::put('/{id}/update-attachment', [BookingController::class, 'updateAttachment']);
//     Route::put('/{id}/payment-method', [BookingController::class, 'updatePaymentMethod']);
//     // Removed duplicate check-availability route
// });

// // User API routes
// Route::prefix('users')->group(function () {
//     Route::get('/', [UserController::class, 'index']);
//     Route::get('/{id}', [UserController::class, 'show']);
//     Route::post('/', [UserController::class, 'store']);
//     Route::put('/{id}', [UserController::class, 'update']);
//     Route::delete('/{id}', [UserController::class, 'destroy']);
//     Route::post('/{id}/deactivate', [UserController::class, 'deactivate']);
// });

// // Room API routes
// Route::prefix('rooms')->group(function () {
//     Route::get('/', [RoomController::class, 'index']);
//     Route::get('/propertyId/{property_id}', [RoomController::class, 'byPropertyId']);
//     Route::get('/{id}', [RoomController::class, 'show']);
//     Route::post('/', [RoomController::class, 'store']);
//     Route::put('/{id}', [RoomController::class, 'update']);
//     Route::delete('/{id}', [RoomController::class, 'destroy']);
// });

// // Profile API routes
// Route::prefix('profile')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/', [AuthController::class, 'profile']);
//     Route::put('/{id}', [AuthController::class, 'updateProfile']);
//     Route::put('/{id}/update-password', [AuthController::class, 'updatePassword']);
//     Route::post('/{id}/profile-picture', [AuthController::class, 'updateProfilePicture']);
// });

// Route::prefix('notifications')->group(function () {
//     Route::get('/', [NotificationController::class, 'getNotifications']);
//     Route::post('/payment', [NotificationController::class, 'paymentNotifications']);
//     Route::post('/mark-as-read', [NotificationController::class, 'markNotificationsAsRead']);
// });
// Route::prefix('doku')->withoutMiddleware(['api.key'])->middleware('App\Http\Middleware\DokuHeaderMiddleware')->group(function () {
//     Route::post('/transfer-va/payment', [NotificationController::class, 'dokuPaymentNotification']);
//     Route::post('/qr-mpm/payment', [NotificationController::class, 'dokuQRPaymentNotification']); 
// });

// END OF LEGACY API ROUTES

// Route::middleware('auth:sanctum')->group(function () {
//     // User info endpoint
//     Route::get('/user', function (Request $request) {
//         return response()->json($request->user());
//     });
    
//     // Profile routes
//     Route::prefix('profile')->group(function () {
//         Route::get('/', [AuthController::class, 'profile']);
//         Route::put('/', [AuthController::class, 'updateProfile']);
//         Route::put('/password', [AuthController::class, 'updatePassword']);
//         Route::post('/profile-picture', [AuthController::class, 'updateProfilePicture']);
//     });
    
//     // Update profile picture
//     Route::post('/user/{id}/profile-picture', [AuthController::class, 'updateProfilePicture']);
    
//     // Booking API routes
//     Route::prefix('booking')->group(function () {
//         Route::get('/', [BookingController::class, 'index']);
//         Route::get('/{id}', [BookingController::class, 'show']);
//         Route::get('/order/{order_id}', [BookingController::class, 'showByOrderId']);
//         Route::get('/userId/{user_id}', [BookingController::class, 'showByUserId']);
//         Route::post('/', [BookingController::class, 'store']);
//         Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.check-availability');
//         Route::put('/{id}', [BookingController::class, 'update']);
//         Route::post('/{id}/upload', [BookingController::class, 'uploadAttachment']);
//         Route::put('/{id}/update-attachment', [BookingController::class, 'updateAttachment']);
//         Route::put('/{id}/payment-method', [BookingController::class, 'updatePaymentMethod']);
//     });
// }); // End of auth:sanctum middleware group