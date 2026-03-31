<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\OrderApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Holomia VR Mobile
|--------------------------------------------------------------------------
| Base URL: /api/v1/...
| Auth: Bearer Token (Sanctum)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // -------------------------------------------------------
    // PUBLIC — không cần đăng nhập
    // -------------------------------------------------------

    // Auth
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');

    // Vé
    Route::get('/tickets', [TicketApiController::class, 'index']);
    Route::get('/tickets/{id}', [TicketApiController::class, 'show']);
    Route::get('/slots', [TicketApiController::class, 'getSlots']); // lấy slot theo vé + ngày

    // -------------------------------------------------------
    // PROTECTED — phải đăng nhập (Bearer Token)
    // -------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/change-password', [AuthApiController::class, 'changePassword']);

        // Đơn hàng
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::post('/orders', [OrderApiController::class, 'store']);       // Đặt vé mới
        Route::get('/orders/{id}', [OrderApiController::class, 'show']);
        Route::post('/orders/{id}/refund', [OrderApiController::class, 'refund']);   // Hủy/hoàn tiền
        Route::get('/orders/{id}/status', [OrderApiController::class, 'checkStatus']); // Polling banking

    });
});