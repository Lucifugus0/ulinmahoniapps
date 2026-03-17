<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\inventory\InvListController;
use App\Http\Controllers\profile\ProfileController;
use App\Http\Controllers\salesorder\NewCustomerRequestController;
use App\Http\Controllers\salesorder\SalesOrderController;
use App\Http\Controllers\SearchProductController;
use App\Http\Controllers\promo\PromoController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\homepage\HomeController;
use App\Http\Controllers\apart\ApartController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\room\RoomController;
use App\Http\Controllers\property\PropertyController;
use App\Http\Controllers\house\HouseController;
use App\Http\Controllers\villa\VillaController;
use App\Http\Controllers\hotel\HotelController;
use App\Http\Controllers\AllPropertiesController;
use App\Http\Controllers\LanguageController;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Email Verification Confirmation Page (no auth required — user may not be logged in)
Route::get('/email/verification-confirmation', function () {
    return view('auth.verify-email-confirmation');
})->name('verification.confirmation');

// Language Switch Route
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

// Route::get('/inventory', [SearchProductController::class, 'index'])->name('search-product');

// Route to serve storage files
Route::get('storage/{path}', function ($path) {
    // Remove any URL parameters if present
    $path = explode('?', $path)[0];
    
    // Build the full file path
    $filePath = storage_path('app/public/' . ltrim($path, '/'));
    
    // Log the path for debugging (check Laravel logs)
    // \Log::info('Accessing file:', ['path' => $filePath]);
    
    if (!File::exists($filePath)) {
        \Log::error('File not found:', ['path' => $filePath]);
        abort(404, 'File not found at path ' . $filePath);
    }

    try {
        $file = File::get($filePath);
        $type = File::mimeType($filePath);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        $response->header("Cache-Control", "public, max-age=31536000, immutable");
        
        return $response;
    } catch (\Exception $e) {
        \Log::error('Error serving file:', [
            'path' => $filePath,
            'error' => $e->getMessage()
        ]);
        abort(500, 'Error serving file');
    }
})->where('path', '.*');


// Google OAuth Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

// Apple OAuth Routes
Route::get('/auth/apple', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToApple'])->name('login.apple');
Route::post('/auth/apple/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleAppleCallback']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Password Reset Routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password', function () {
        return view('auth.reset-password');
    })->name('password.reset'); 

    // Email Verification Notice
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
        // \Log::info('Verification link clicked', [
        //     'id' => $id,
        //     'hash' => $hash,
        //     'url' => $request->fullUrl(),
        //     'signature_valid' => $request->hasValidSignature(),
        //     'signature' => $request->query('signature')
        // ]);
        
        // Forward to our custom controller
        return app(VerifyEmailController::class)($request, $id, $hash);
    })->middleware(['signed', 'throttle:6,1'])
      ->name('verification.verify')
      ->withoutMiddleware(['auth']);

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Route::redirect('/', 'login');
// Homepage - serve directly at root URL for clean URLs
Route::get('/', [HomeController::class, 'index'])->name('home');

// ========================================
// Localized Routes Group
// Note: SetLocale middleware is already applied globally in Kernel.php
// ========================================

// ========================================
// Indonesian Routes (ID)
// ========================================
Route::prefix('id')->name('id.')->group(function () {
    // Authentication
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Homepage
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

    // Information Pages
    Route::view('/sewa', 'pages.info.indonesia.sewa')->name('sewa');
    Route::view('/kerjasama', 'pages.info.indonesia.kerjasama')->name('kerjasama');
    Route::view('/business', 'pages.info.indonesia.business')->name('business');
    Route::view('/tentang', 'pages.info.indonesia.tentang')->name('tentang');

    // Properties
    Route::get('/properties', [AllPropertiesController::class, 'index'])->name('properties.index');
    Route::get('/properties/search', [PropertyController::class, 'search'])->name('properties.search');
    Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

    // Houses
    Route::get('/houses', [HouseController::class, 'index'])->name('houses');
    Route::get('/houses/{id}', [HouseController::class, 'show'])->name('houses.show');
    Route::get('/houses/rooms', [RoomController::class, 'index'])->name('houses.rooms');

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('property/{propertyId}', [RoomController::class, 'showPropertyRooms'])->name('property.show');
        Route::post('property/{propertyId}', [RoomController::class, 'store'])->name('store');
        Route::put('{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('{id}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('{slug}', [RoomController::class, 'show'])->name('show');
        Route::post('book', [RoomController::class, 'book'])->name('book');
    });

    // Apartments
    Route::get('/apartments', [ApartController::class, 'index'])->name('apartments');
    Route::get('/apartments/{id}', [ApartController::class, 'show'])->name('apartments.show');

    // Villas
    Route::get('/villas', [VillaController::class, 'index'])->name('villas');
    Route::get('/villas/{id}', [VillaController::class, 'show'])->name('villas.show');

    // Hotels
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels');
    Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

    // Promos
    Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
    Route::get('/promo/{id}', [PromoController::class, 'show'])->name('promos.show');

    // Bookings - Authentication Required
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });

    // Bookings - Email Verification Required
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{id}/upload-attachment', [BookingController::class, 'uploadAttachment'])->name('bookings.upload-attachment');
        Route::get('/bookings/{id}/view-attachment', [BookingController::class, 'generateAttachmentUrl'])->name('bookings.view-attachment');
        Route::post('/bookings/{id}/update-payment', [BookingController::class, 'updatePaymentMethod'])->name('bookings.update-payment');
        Route::post('/bookings/{id}/mark-expired', [BookingController::class, 'markExpired'])->name('bookings.mark-expired');
        Route::get('/payment/{booking:order_id}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    });

    // Legal Pages
    Route::get('/privacy-policy', function () {
        return view('pages.privacy-policy.privacy-policy');
    })->name('privacy-policy');
    Route::get('/terms-of-services', function () {
        return view('pages.terms-of-services.terms-of-services');
    })->name('terms-of-services');
    Route::get('/rental-agreement', function () {
        return view('pages.rental-agreement.rental-agreement');
    })->name('rental-agreement');
});

// ========================================
// English Routes (EN)
// ========================================
Route::prefix('en')->name('en.')->group(function () {
    // Authentication
    Route::get('/login', function () {
        return view('auth.en.login');
    })->name('login');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Homepage
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

    // Information Pages
    Route::view('/rental', 'pages.info.english.rental')->name('rental');
    Route::view('/partnership', 'pages.info.english.partnership')->name('partnership');
    Route::view('/business', 'pages.info.english.business')->name('business');
    Route::view('/about', 'pages.info.english.about')->name('about');

    // Properties
    Route::get('/properties', [AllPropertiesController::class, 'index'])->name('properties.index');
    Route::get('/properties/search', [PropertyController::class, 'search'])->name('properties.search');
    Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

    // Houses
    Route::get('/houses', [HouseController::class, 'index'])->name('houses');
    Route::get('/houses/{id}', [HouseController::class, 'show'])->name('houses.show');
    Route::get('/houses/rooms', [RoomController::class, 'index'])->name('houses.rooms');

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('property/{propertyId}', [RoomController::class, 'showPropertyRooms'])->name('property.show');
        Route::post('property/{propertyId}', [RoomController::class, 'store'])->name('store');
        Route::put('{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('{id}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('{slug}', [RoomController::class, 'show'])->name('show');
        Route::post('book', [RoomController::class, 'book'])->name('book');
    });

    // Apartments
    Route::get('/apartments', [ApartController::class, 'index'])->name('apartments');
    Route::get('/apartments/{id}', [ApartController::class, 'show'])->name('apartments.show');

    // Villas
    Route::get('/villas', [VillaController::class, 'index'])->name('villas');
    Route::get('/villas/{id}', [VillaController::class, 'show'])->name('villas.show');

    // Hotels
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels');
    Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

    // Promos
    Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
    Route::get('/promo/{id}', [PromoController::class, 'show'])->name('promos.show');

    // Bookings - Authentication Required
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });

    // Bookings - Email Verification Required
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{id}/upload-attachment', [BookingController::class, 'uploadAttachment'])->name('bookings.upload-attachment');
        Route::get('/bookings/{id}/view-attachment', [BookingController::class, 'generateAttachmentUrl'])->name('bookings.view-attachment');
        Route::post('/bookings/{id}/update-payment', [BookingController::class, 'updatePaymentMethod'])->name('bookings.update-payment');
        Route::post('/bookings/{id}/mark-expired', [BookingController::class, 'markExpired'])->name('bookings.mark-expired');
        Route::get('/payment/qris', fn() => view('pages.payment.qris-show'))->name('payment.qris-show');
        Route::get('/payment/{booking:order_id}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    });

    // Legal Pages
    Route::get('/privacy-policy', function () {
        return view('pages.privacy-policy.privacy-policy');
    })->name('privacy-policy');
    Route::get('/terms-of-services', function () {
        return view('pages.terms-of-services.terms-of-services');
    })->name('terms-of-services');
    Route::get('/rental-agreement', function () {
        return view('pages.rental-agreement.rental-agreement');
    })->name('rental-agreement');
});

// ========================================
// Chinese Routes (ZH)
// Mirrors EN routes for Simplified Chinese locale.
// Uses same views/controllers — translations handled via lang/zh/ files.
// ========================================
Route::prefix('zh')->name('zh.')->group(function () {
    // Authentication
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Homepage
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

    // Information Pages
    Route::view('/rental', 'pages.info.english.rental')->name('rental');
    Route::view('/partnership', 'pages.info.english.partnership')->name('partnership');
    Route::view('/business', 'pages.info.english.business')->name('business');
    Route::view('/about', 'pages.info.english.about')->name('about');

    // Properties
    Route::get('/properties', [AllPropertiesController::class, 'index'])->name('properties.index');
    Route::get('/properties/search', [PropertyController::class, 'search'])->name('properties.search');
    Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

    // Houses
    Route::get('/houses', [HouseController::class, 'index'])->name('houses');
    Route::get('/houses/{id}', [HouseController::class, 'show'])->name('houses.show');
    Route::get('/houses/rooms', [RoomController::class, 'index'])->name('houses.rooms');

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('property/{propertyId}', [RoomController::class, 'showPropertyRooms'])->name('property.show');
        Route::post('property/{propertyId}', [RoomController::class, 'store'])->name('store');
        Route::put('{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('{id}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('{slug}', [RoomController::class, 'show'])->name('show');
        Route::post('book', [RoomController::class, 'book'])->name('book');
    });

    // Apartments
    Route::get('/apartments', [ApartController::class, 'index'])->name('apartments');
    Route::get('/apartments/{id}', [ApartController::class, 'show'])->name('apartments.show');

    // Villas
    Route::get('/villas', [VillaController::class, 'index'])->name('villas');
    Route::get('/villas/{id}', [VillaController::class, 'show'])->name('villas.show');

    // Hotels
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels');
    Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

    // Promos
    Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
    Route::get('/promo/{id}', [PromoController::class, 'show'])->name('promos.show');

    // Bookings - Authentication Required
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });

    // Bookings - Email Verification Required
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{id}/upload-attachment', [BookingController::class, 'uploadAttachment'])->name('bookings.upload-attachment');
        Route::get('/bookings/{id}/view-attachment', [BookingController::class, 'generateAttachmentUrl'])->name('bookings.view-attachment');
        Route::post('/bookings/{id}/update-payment', [BookingController::class, 'updatePaymentMethod'])->name('bookings.update-payment');
        Route::post('/bookings/{id}/mark-expired', [BookingController::class, 'markExpired'])->name('bookings.mark-expired');
        Route::get('/payment/qris', fn() => view('pages.payment.qris-show'))->name('payment.qris-show');
        Route::get('/payment/{booking:order_id}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    });

    // Legal Pages
    Route::get('/privacy-policy', function () {
        return view('pages.privacy-policy.privacy-policy');
    })->name('privacy-policy');
    Route::get('/terms-of-services', function () {
        return view('pages.terms-of-services.terms-of-services');
    })->name('terms-of-services');
    Route::get('/rental-agreement', function () {
        return view('pages.rental-agreement.rental-agreement');
    })->name('rental-agreement');
});

// ========================================
// Non-localized Routes (Shared Resources)
// ========================================

// Attachment viewing with signed URL (shared across locales)
Route::get('/attachments/{id}', [BookingController::class, 'viewAttachment'])
    ->name('attachments.view')
    ->middleware('signed');

// Default Homepage (redirect to root for clean URL)
Route::redirect('/homepage', '/')->name('homepage');

// Default routes (redirect to Indonesian locale)
Route::redirect('/sewa', '/id/sewa');
Route::redirect('/kerjasama', '/id/kerjasama');
Route::redirect('/business', '/id/business');
Route::redirect('/tentang', '/id/tentang');

// Non-localized property routes (for backward compatibility)
Route::get('/properties', [AllPropertiesController::class, 'index'])->name('properties.index');
Route::get('/properties/search', [PropertyController::class, 'search'])->name('properties.search');
Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.show');

Route::get('/houses', [HouseController::class, 'index'])->name('houses');
Route::get('/houses/{id}', [HouseController::class, 'show'])->name('houses.show');
Route::get('/houses/rooms', [RoomController::class, 'index'])->name('houses.rooms');

Route::prefix('rooms')->group(function () {
    Route::get('property/{propertyId}', [RoomController::class, 'showPropertyRooms'])->name('rooms.property.show');
    Route::post('property/{propertyId}', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('{id}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::get('{slug}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('book', [RoomController::class, 'book'])->name('rooms.book');
});

Route::get('/apartments', [ApartController::class, 'index'])->name('apartments');
Route::get('/apartments/{id}', [ApartController::class, 'show'])->name('apartments.show');

Route::get('/villas', [VillaController::class, 'index'])->name('villas');
Route::get('/villas/{id}', [VillaController::class, 'show'])->name('villas.show');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels');
Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
Route::get('/promo/{id}', [PromoController::class, 'show'])->name('promos.show');

// Bookings Routes (non-localized for backward compatibility)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/upload-attachment', [BookingController::class, 'uploadAttachment'])->name('bookings.upload-attachment');
    Route::get('/bookings/{id}/view-attachment', [BookingController::class, 'generateAttachmentUrl'])->name('bookings.view-attachment');
    Route::post('/bookings/{id}/update-payment', [BookingController::class, 'updatePaymentMethod'])->name('bookings.update-payment');
    Route::post('/bookings/{id}/mark-expired', [BookingController::class, 'markExpired'])->name('bookings.mark-expired');
    Route::get('/payment/qris', fn() => view('pages.payment.qris-show'))->name('payment.qris-show');
    Route::get('/payment/{booking:order_id}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
});

// Legal Pages (non-localized)
Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-services', function () {
    return view('pages.terms-of-services.terms-of-services');
})->name('terms-of-services');

Route::get('/rental-agreement', function () {
    return view('pages.rental-agreement.rental-agreement');
})->name('rental-agreement');

// OLD ROUTES
// Authenticated routes that require email verification
Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/getdata', [DashboardController::class, 'getData'])->name('dashboard.sales');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/dashboard/fintech', [DashboardController::class, 'fintech'])->name('fintech');
    Route::get('/ecommerce/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/ecommerce/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/ecommerce/invoices', [InvoiceController::class, 'index'])->name('invoices');
    
    Route::post('/update-gcal', [ProfileController::class, 'update'])->name('update.gcal');
    Route::prefix('inventory')->group(function () {
        Route::get('/invlist', [InvListController::class, 'index'])->name('invlist');
        Route::get('/invlist/getdata', [InvListController::class, 'getData'])->name('invlist.getdata');
        Route::get('/invlist/getdetail/{code}', [InvListController::class, 'getDetail'])->name('invlist.getdetail');
        Route::get('/invlist/updatepage/{code}', [InvListController::class, 'updatePage'])->name('invlist.updatepage');
        Route::post('/invlist/update/{code}', [InvListController::class, 'update'])->name('invlist.update');
        Route::get('/invlist/file1/{code}', [InvListController::class, 'viewPhoto1'])->name('invlist.photo1');
        Route::get('/invlist/file2/{code}', [InvListController::class, 'viewPhoto2'])->name('invlist.photo2');
        Route::get('/invlist/file3/{code}', [InvListController::class, 'viewPhoto3'])->name('invlist.photo3');
    });

    // SalesOrders
    Route::prefix('sales')->group(function () {
        Route::get('/sales-order', [SalesOrderController::class, 'index'])->name('sales-order');
        Route::get('/sales-order/getdata', [SalesOrderController::class, 'getData'])->name('sales-order.getdata');
        Route::get('/sales-order/form', [SalesOrderController::class, 'form'])->name('sales-order.form');
        Route::post('/sales-order/create', [SalesOrderController::class, 'create'])->name('sales-order.create');
        Route::get('/sales-order/getcustomer/customerId', [SalesOrderController::class, 'getCustomer'])->name('create.getcustomer');
        Route::get('/sales-order/getproduct', [SalesOrderController::class, 'getProduct'])->name('create.getproduct');
        Route::get('/sales-order/getdetail/{salesId}', [SalesOrderController::class, 'getDetail'])->name('sales-order.getdetail');
        Route::post('/sales-order/update/{salesId}', [SalesOrderController::class, 'updateSo'])->name('sales-order.updateso');
        Route::get('/sales-order/print/{salesId}', [SalesOrderController::class, 'print'])->name('sales-order.print');

        // Route::get('/new-customer', [NewCustomerRequestController::class, 'index'])->name('new-customer');
        // Route::get('/new-customer/getdata', [NewCustomerRequestController::class, 'getData'])->name('new-customer.getdata');
        // Route::post('/new-customer/create', [NewCustomerRequestController::class, 'create'])->name('new-customer.create');
    });

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    
    Route::get('/ecommerce/shop', function () {
        return view('pages/ecommerce/shop');
    })->name('shop');
    Route::get('/ecommerce/shop-2', function () {
        return view('pages/ecommerce/shop-2');
    })->name('shop-2');
    Route::get('/ecommerce/product', function () {
        return view('pages/ecommerce/product');
    })->name('product');
    Route::get('/ecommerce/cart', function () {
        return view('pages/ecommerce/cart');
    })->name('cart');
    Route::get('/ecommerce/cart-2', function () {
        return view('pages/ecommerce/cart-2');
    })->name('cart-2');
    Route::get('/ecommerce/cart-3', function () {
        return view('pages/ecommerce/cart-3');
    })->name('cart-3');
    Route::get('/ecommerce/pay', function () {
        return view('pages/ecommerce/pay');
    })->name('pay');
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
    Route::get('/community/users-tabs', [MemberController::class, 'indexTabs'])->name('users-tabs');
    Route::get('/community/users-tiles', [MemberController::class, 'indexTiles'])->name('users-tiles');
    Route::get('/community/profile', function () {
        return view('pages/community/profile');
    })->name('profile');
    Route::get('/community/feed', function () {
        return view('pages/community/feed');
    })->name('feed');
    Route::get('/community/forum', function () {
        return view('pages/community/forum');
    })->name('forum');
    Route::get('/community/forum-post', function () {
        return view('pages/community/forum-post');
    })->name('forum-post');
    Route::get('/community/meetups', function () {
        return view('pages/community/meetups');
    })->name('meetups');
    Route::get('/community/meetups-post', function () {
        return view('pages/community/meetups-post');
    })->name('meetups-post');
    Route::get('/finance/cards', function () {
        return view('pages/finance/credit-cards');
    })->name('credit-cards');
    Route::get('/finance/transactions', [TransactionController::class, 'index01'])->name('transactions');
    Route::get('/finance/transaction-details', [TransactionController::class, 'index02'])->name('transaction-details');
    Route::get('/job/job-listing', [JobController::class, 'index'])->name('job-listing');
    Route::get('/job/job-post', function () {
        return view('pages/job/job-post');
    })->name('job-post');
    Route::get('/job/company-profile', function () {
        return view('pages/job/company-profile');
    })->name('company-profile');
    Route::get('/messages', function () {
        return view('pages/messages');
    })->name('messages');
    Route::get('/inbox', function () {
        return view('pages/inbox');
    })->name('inbox');
    Route::get('/settings/account', function () {
        return view('pages/settings/account');
    })->name('account');
    Route::get('/settings/notifications', function () {
        return view('pages/settings/notifications');
    })->name('notifications');
    Route::get('/settings/apps', function () {
        return view('pages/settings/apps');
    })->name('apps');
    Route::get('/settings/plans', function () {
        return view('pages/settings/plans');
    })->name('plans');
    Route::get('/settings/billing', function () {
        return view('pages/settings/billing');
    })->name('billing');
    Route::get('/settings/feedback', function () {
        return view('pages/settings/feedback');
    })->name('feedback');
    Route::get('/utility/changelog', function () {
        return view('pages/utility/changelog');
    })->name('changelog');
    Route::get('/utility/roadmap', function () {
        return view('pages/utility/roadmap');
    })->name('roadmap');
    Route::get('/utility/faqs', function () {
        return view('pages/utility/faqs');
    })->name('faqs');
    Route::get('/utility/empty-state', function () {
        return view('pages/utility/empty-state');
    })->name('empty-state');
    Route::get('/utility/404', function () {
        return view('pages/utility/404');
    })->name('404');
    Route::get('/utility/knowledge-base', function () {
        return view('pages/utility/knowledge-base');
    })->name('knowledge-base');
    Route::get('/onboarding-01', function () {
        return view('pages/onboarding-01');
    })->name('onboarding-01');
    Route::get('/onboarding-02', function () {
        return view('pages/onboarding-02');
    })->name('onboarding-02');
    Route::get('/onboarding-03', function () {
        return view('pages/onboarding-03');
    })->name('onboarding-03');
    Route::get('/onboarding-04', function () {
        return view('pages/onboarding-04');
    })->name('onboarding-04');
    Route::get('/component/button', function () {
        return view('pages/component/button-page');
    })->name('button-page');
    Route::get('/component/form', function () {
        return view('pages/component/form-page');
    })->name('form-page');
    Route::get('/component/dropdown', function () {
        return view('pages/component/dropdown-page');
    })->name('dropdown-page');
    Route::get('/component/alert', function () {
        return view('pages/component/alert-page');
    })->name('alert-page');
    Route::get('/component/modal', function () {
        return view('pages/component/modal-page');
    })->name('modal-page');
    Route::get('/component/pagination', function () {
        return view('pages/component/pagination-page');
    })->name('pagination-page');
    Route::get('/component/tabs', function () {
        return view('pages/component/tabs-page');
    })->name('tabs-page');
    Route::get('/component/breadcrumb', function () {
        return view('pages/component/breadcrumb-page');
    })->name('breadcrumb-page');
    Route::get('/component/badge', function () {
        return view('pages/component/badge-page');
    })->name('badge-page');
    Route::get('/component/avatar', function () {
        return view('pages/component/avatar-page');
    })->name('avatar-page');
    Route::get('/component/tooltip', function () {
        return view('pages/component/tooltip-page');
    })->name('tooltip-page');
    Route::get('/component/accordion', function () {
        return view('pages/component/accordion-page');
    })->name('accordion-page');
    Route::get('/component/icons', function () {
        return view('pages/component/icons-page');
    })->name('icons-page');
    
    Route::fallback(function () {
        return view('pages/utility/404');
    });

});
// Route::get('/inventory/getdata', [SearchProductController::class, 'getData'])->name('search-product.getdata');
// Route::get('/inventory/getdetail/{code}', [SearchProductController::class, 'getDetail'])->name('search-product.getdetail');
// Route::get('/coming-soon', [HomeController::class, 'comingSoon'])->name('coming-soon');
