<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RegionController;
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

    // 0.1 Indonesian Administrative Regions & Geocoding
    Route::get('/regions/provinces', [RegionController::class, 'provinces']);
    Route::get('/regions/regencies/{province_id}', [RegionController::class, 'regencies']);
    Route::get('/regions/districts/{regency_id}', [RegionController::class, 'districts']);
    Route::get('/regions/villages/{district_id}', [RegionController::class, 'villages']);
    Route::get('/regions/reverse-geocode', [RegionController::class, 'reverseGeocode']);

    // 1. Authentication & OTP Verification
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

    // 2. Banners & Categories & Vouchers
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/vouchers/available', [\App\Http\Controllers\Api\VoucherApiController::class, 'available']);

    // 2.1 AI Customer Support Assistant (Powered by Gemini)
    Route::post('/ai-chat', [\App\Http\Controllers\AiAssistantController::class, 'chat']);

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

        // User Wallet (NitipPay)
        Route::get('/wallet', [\App\Http\Controllers\Api\WalletApiController::class, 'index']);
        Route::post('/wallet/topup', [\App\Http\Controllers\Api\WalletApiController::class, 'topUp']);

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
        Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking']);
        Route::post('/orders/checkout', [OrderController::class, 'checkout']);
        Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
        Route::post('/orders/{id}/confirm', [OrderController::class, 'confirmReceived']);
        Route::post('/orders/{id}/reviews', [OrderController::class, 'storeReview']);
        Route::post('/vouchers/validate', [OrderController::class, 'validateVoucher']);
        Route::post('/products/{id}/discussions', [ProductController::class, 'storeDiscussion']);
        Route::post('/products/{id}/discussions/{discussion_id}/reply', [ProductController::class, 'replyDiscussion']);

        // Courier Delivery Routes
        Route::get('/courier/orders', [\App\Http\Controllers\Api\CourierApiController::class, 'index']);
        Route::post('/courier/orders/{id}/pickup', [\App\Http\Controllers\Api\CourierApiController::class, 'pickup']);
        Route::post('/courier/orders/{id}/deliver', [\App\Http\Controllers\Api\CourierApiController::class, 'deliver']);

        // Midtrans Core API Direct Payment
        Route::post('/payment/midtrans/charge', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'charge']);
        Route::get('/orders/{id}/payment-status', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'status']);
        Route::post('/orders/{id}/simulate-paid', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'simulatePaid']);
    });

    // Public Polling & Webhook Status Check (No Auth needed for client poll)
    Route::get('/orders/{id}/payment-status', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'status']);
    Route::post('/orders/{id}/simulate-paid', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'simulatePaid']);
    Route::post('/payment/midtrans/charge', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'charge']);
});
