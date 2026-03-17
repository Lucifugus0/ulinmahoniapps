<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chat API Routes for Android App
Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    // Conversations
    Route::get('/conversations', [\App\Http\Controllers\Api\ChatApiController::class, 'index']);
    Route::post('/conversations', [\App\Http\Controllers\Api\ChatApiController::class, 'store']);
    Route::get('/conversations/{id}', [\App\Http\Controllers\Api\ChatApiController::class, 'show']);
    Route::put('/conversations/{id}', [\App\Http\Controllers\Api\ChatApiController::class, 'update']);
    Route::delete('/conversations/{id}', [\App\Http\Controllers\Api\ChatApiController::class, 'destroy']);

    // Messages
    Route::get('/conversations/{id}/messages', [\App\Http\Controllers\Api\ChatApiController::class, 'getMessages']);
    Route::post('/conversations/{id}/messages', [\App\Http\Controllers\Api\ChatApiController::class, 'sendMessage']);
    Route::post('/messages/{id}/read', [\App\Http\Controllers\Api\ChatApiController::class, 'markAsRead']);
    Route::post('/conversations/{id}/read-all', [\App\Http\Controllers\Api\ChatApiController::class, 'markAllAsRead']);

    // Attachments
    Route::post('/messages/{id}/attachments', [\App\Http\Controllers\Api\ChatApiController::class, 'uploadAttachment']);

    // Typing Indicator
    Route::post('/conversations/{id}/typing', [\App\Http\Controllers\Api\ChatApiController::class, 'setTypingStatus']);

    // Search
    Route::get('/search', [\App\Http\Controllers\Api\ChatApiController::class, 'searchMessages']);
});

// Parking Fees API
Route::get('/parking-fees/{propertyId}', [\App\Http\Controllers\Payment\ParkingPaymentController::class, 'getParkingFees']);

// Booking Attachment API
Route::prefix('v1')->group(function () {
    Route::get('/booking/{id}', [\App\Http\Controllers\Payment\PaymentController::class, 'getBookingAttachment'])->name('api.booking.attachment');
});
