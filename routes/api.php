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
use App\Http\Controllers\Api\AdminApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NitipDong Mobile REST API Routes (Version 1)
|--------------------------------------------------------------------------
*/

// Midtrans Public Webhook Notification Endpoints (Accessible via https://budayakita.com/api/midtrans/notification)
Route::match(['get', 'post'], '/midtrans/notification', [\App\Http\Controllers\MidtransPaymentController::class, 'handleNotification'])->name('api.midtrans.notification');
Route::match(['get', 'post'], '/midtrans/callback', [\App\Http\Controllers\MidtransPaymentController::class, 'handleNotification'])->name('api.midtrans.callback');
Route::match(['get', 'post'], '/payment/notification', [\App\Http\Controllers\MidtransPaymentController::class, 'handleNotification'])->name('api.payment.notification');

Route::prefix('v1')->group(function () {

    // Midtrans Notification v1 alias
    Route::match(['get', 'post'], '/midtrans/notification', [\App\Http\Controllers\MidtransPaymentController::class, 'handleNotification']);
    Route::match(['get', 'post'], '/payment/notification', [\App\Http\Controllers\MidtransPaymentController::class, 'handleNotification']);

    // PUBLIC API ENDPOINTS

    // 0. App System Status & Maintenance Mode
    Route::get('/system/status', [SystemConfigController::class, 'status']);

    // 0.1 Indonesian Administrative Regions & Geocoding
    Route::get('/regions/provinces', [RegionController::class, 'provinces']);
    Route::get('/regions/regencies/{province_id}', [RegionController::class, 'regencies']);
    Route::get('/regions/districts/{regency_id}', [RegionController::class, 'districts']);
    Route::get('/regions/villages/{district_id}', [RegionController::class, 'villages']);
    Route::get('/regions/reverse-geocode', [RegionController::class, 'reverseGeocode']);

    // 1. Authentication & OTP Verification
    Route::middleware('throttle:api-auth')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleApiGoogleLogin']);
        Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
    });

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

    // AUTHENTICATED USER ENDPOINTS (SANCTUM PROTECTED)

    Route::middleware('auth:sanctum')->group(function () {

        // User Profile & Logout & Biometric Lock
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/user/biometric/toggle', [AuthController::class, 'toggleBiometric']);

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

        // Courier Delivery Partner Endpoints
        Route::prefix('courier')->group(function () {
            Route::post('/register', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'registerCourier']);
            Route::get('/warehouses', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'warehouses']);
            Route::get('/deliveries', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'index']);
            Route::get('/deliveries/{id}', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'show']);
            Route::post('/deliveries/{id}/accept', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'acceptTask']);
            Route::post('/deliveries/{id}/update-gps', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'updateGps']);
            Route::post('/deliveries/{id}/complete', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'completeDelivery']);
            Route::get('/statistics', [\App\Http\Controllers\Api\CourierDeliveryController::class, 'statistics']);
        });

        // Seller Center Mobile Endpoints
        Route::prefix('seller')->group(function () {
            Route::post('/register', [\App\Http\Controllers\Api\SellerApiController::class, 'registerStore']);
            Route::get('/dashboard', [\App\Http\Controllers\Api\SellerApiController::class, 'dashboard']);
            Route::get('/products', [\App\Http\Controllers\Api\SellerApiController::class, 'products']);
            Route::post('/products', [\App\Http\Controllers\Api\SellerApiController::class, 'storeProduct']);
            Route::delete('/products/{id}', [\App\Http\Controllers\Api\SellerApiController::class, 'deleteProduct']);
            Route::get('/orders', [\App\Http\Controllers\Api\SellerApiController::class, 'orders']);
            Route::patch('/orders/{id}/status', [\App\Http\Controllers\Api\SellerApiController::class, 'updateOrderStatus']);
        });

        // Admin & Super Admin Mobile Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\AdminApiController::class, 'dashboard']);
            Route::get('/users', [\App\Http\Controllers\Api\AdminApiController::class, 'users']);
            Route::get('/stores', [\App\Http\Controllers\Api\AdminApiController::class, 'stores']);
            Route::post('/stores/{id}/toggle-status', [\App\Http\Controllers\Api\AdminApiController::class, 'toggleStoreStatus']);
            Route::get('/system/maintenance', [\App\Http\Controllers\Api\AdminApiController::class, 'getMaintenanceStatus']);
            Route::post('/system/maintenance', [\App\Http\Controllers\Api\AdminApiController::class, 'toggleMaintenance']);
        });

        // Customer Live Map Tracking
        Route::get('/orders/{id}/live-tracking', [\App\Http\Controllers\Api\LiveTrackingController::class, 'getLiveTracking']);

        // Midtrans Core API Direct Payment
        Route::post('/payment/midtrans/charge', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'charge']);
        Route::get('/orders/{id}/payment-status', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'status']);
        Route::post('/orders/{id}/simulate-paid', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'simulatePaid']);

        // Operational Admin Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [AdminApiController::class, 'dashboardStats']);
            Route::get('/stores', [AdminApiController::class, 'getPendingStores']);
            Route::post('/stores/{id}/approve', [AdminApiController::class, 'approveStore']);
            Route::post('/stores/{id}/reject', [AdminApiController::class, 'rejectStore']);
            
            Route::get('/products', [AdminApiController::class, 'getProducts']);
            Route::post('/products/{id}/toggle', [AdminApiController::class, 'toggleProductStatus']);
            
            Route::get('/categories', [AdminApiController::class, 'getCategories']);
            Route::post('/categories', [AdminApiController::class, 'storeCategory']);
            Route::put('/categories/{id}', [AdminApiController::class, 'updateCategory']);
            Route::delete('/categories/{id}', [AdminApiController::class, 'deleteCategory']);
            
            Route::get('/flash-sales', [AdminApiController::class, 'getFlashSales']);
            Route::post('/flash-sales', [AdminApiController::class, 'storeFlashSale']);
            Route::post('/flash-sales/{id}/toggle', [AdminApiController::class, 'toggleFlashSale']);
            Route::post('/flash-sales/{id}/items', [AdminApiController::class, 'addFlashSaleItem']);
            Route::delete('/flash-sales/{id}/items/{itemId}', [AdminApiController::class, 'removeFlashSaleItem']);
        });
    });

    // Public Polling & Live Map Tracking Fallback
    Route::get('/orders/{id}/live-tracking', [\App\Http\Controllers\Api\LiveTrackingController::class, 'getLiveTracking']);
    Route::get('/orders/{id}/payment-status', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'status'])->middleware('throttle:payment-polling');
});
