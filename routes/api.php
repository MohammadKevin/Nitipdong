<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SystemConfigController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NitipDong Mobile REST API Routes (Version 1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ══════════════════════════════════════════════════
    // PUBLIC API ENDPOINTS
    // ══════════════════════════════════════════════════

    // 0. App System Status & Maintenance Mode
    Route::get('/system/status', [SystemConfigController::class, 'status']);

    // 1. Authentication & OTP Verification
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

    // 2. Banners & Categories
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // 3. Products Catalog & Flash Sale
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/flash-sale', [ProductController::class, 'flashSale']);
    Route::get('/products/{id}', [ProductController::class, 'show']);


    // ══════════════════════════════════════════════════
    // AUTHENTICATED USER ENDPOINTS (SANCTUM PROTECTED)
    // ══════════════════════════════════════════════════
    Route::middleware('auth:sanctum')->group(function () {

        // User Profile & Logout
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // User Shipping Address
        Route::get('/addresses/primary', [AddressController::class, 'primary']);
        Route::post('/addresses', [AddressController::class, 'storeOrUpdate']);

        // Cart Management
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);
        Route::get('/cart/count', [CartController::class, 'count']);

        // Orders & Checkout
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/checkout', [OrderController::class, 'checkout']);
        Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
        Route::post('/vouchers/validate', [OrderController::class, 'validateVoucher']);
        Route::post('/products/{id}/discussions', [ProductController::class, 'storeDiscussion']);
        Route::post('/products/{id}/discussions/{discussion_id}/reply', [ProductController::class, 'replyDiscussion']);
    });
});
