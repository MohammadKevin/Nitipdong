<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\OrderMonitoringController as AdminOrderMonitoringController;
use App\Http\Controllers\Admin\ProductModerationController;
use App\Http\Controllers\Admin\StoreApprovalController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\ComplaintController as CustomerComplaintController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\StoreRegistrationController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\ProductDiscussionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\ComplaintController as SellerComplaintController;
use App\Http\Controllers\Seller\OrderManagementController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\ReviewController as SellerReviewController;
use App\Http\Controllers\Seller\StoreSettingsController;
use App\Http\Controllers\Seller\VoucherController as SellerVoucherController;
use App\Http\Controllers\Seller\WalletController as SellerWalletController;
use App\Http\Controllers\StoreFrontController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;
use App\Http\Controllers\SuperAdmin\WithdrawalController as SuperAdminWithdrawalController;
use App\Http\Controllers\SuperAdminController;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Webhook Payment Callbacks (Exempt from CSRF)
Route::post('/api/midtrans/notification', [MidtransPaymentController::class, 'handleNotification'])->name('midtrans.notification');
Route::post('/api/payment/notification', [MidtransPaymentController::class, 'handleNotification'])->name('payment.webhook');

// Super Admin / System Action to Reset Transactions & Reviews (Keeping Products, Users & Stock Intact)
Route::get('/system/reset-transactions', function (\Illuminate\Http\Request $request) {
    $isSuperAdmin = Auth::check() && in_array(Auth::user()->role, ['super_admin', 'admin']);
    $isValidSecret = $request->query('key') === 'nitipdong2026reset';

    if (!$isSuperAdmin && !$isValidSecret) {
        abort(403, 'Akses ditolak. Tindakan ini hanya dapat dilakukan oleh Super Admin.');
    }

    \Illuminate\Support\Facades\Artisan::call('app:reset-transactions', ['--force' => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();

    return response()->json([
        'success'   => true,
        'message'   => 'Reset database transaksi, rating, dan ulasan berhasil dijalankan.',
        'details'   => $output,
        'preserved' => [
            'users'      => 'Aman & Utuh',
            'stores'     => 'Aman & Utuh',
            'products'   => 'Aman & Utuh',
            'stock'      => 'Aman & Utuh',
            'categories' => 'Aman & Utuh',
        ]
    ]);
})->name('system.reset-transactions');

// Smart Download Route for Mobile Apps (Direct Fast Local Download from DomaiNesia)
Route::get('/download/app', function (\Illuminate\Http\Request $request) {
    $userAgent = strtolower($request->header('User-Agent', ''));
    $isIos = str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad') || str_contains($userAgent, 'ipod');
    $version = env('APP_MOBILE_LATEST_VERSION', '1.9.2.3');

    if ($isIos) {
        $paths = [
            public_path('downloads/nitipdong.ipa'),
            public_path('downloads/NitipDong-latest.ipa'),
            base_path('../public_html/downloads/nitipdong.ipa'),
            base_path('../public_html/downloads/NitipDong-latest.ipa'),
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) {
                return response()->download($p, "NitipDong-v{$version}.ipa", [
                    'Content-Type'        => 'application/octet-stream',
                    'Content-Disposition' => "attachment; filename=\"NitipDong-v{$version}.ipa\"",
                    'Cache-Control'       => 'no-cache, no-store, must-revalidate, max-age=0',
                    'Pragma'              => 'no-cache',
                    'Expires'             => '0',
                ]);
            }
        }
        return redirect()->away('https://github.com/MohammadKevin/Nitipdong/releases/download/latest/NitipDong-latest.ipa', 302);
    }

    // Android APK Direct Download from Local DomaiNesia Server (Instant & Ultra-fast)
    $paths = [
        public_path('downloads/nitipdong.apk'),
        public_path('downloads/NitipDong-latest.apk'),
        base_path('../public_html/downloads/nitipdong.apk'),
        base_path('../public_html/downloads/NitipDong-latest.apk'),
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            return response()->download($p, "NitipDong-v{$version}.apk", [
                'Content-Type'        => 'application/vnd.android.package-archive',
                'Content-Disposition' => "attachment; filename=\"NitipDong-v{$version}.apk\"",
                'Cache-Control'       => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ]);
        }
    }

    return redirect()->away('https://github.com/MohammadKevin/Nitipdong/releases/download/latest/NitipDong-latest.apk', 302);
})->name('app.download');

// Direct Android Download Route
Route::get('/download/android', function () {
    $version = env('APP_MOBILE_LATEST_VERSION', '2.5.1');
    $paths = [
        public_path('downloads/nitipdong.apk'),
        public_path('downloads/NitipDong-latest.apk'),
        base_path('../public_html/downloads/nitipdong.apk'),
        base_path('../public_html/downloads/NitipDong-latest.apk'),
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            return response()->download($p, "NitipDong-v{$version}.apk", [
                'Content-Type'        => 'application/vnd.android.package-archive',
                'Content-Disposition' => "attachment; filename=\"NitipDong-v{$version}.apk\"",
                'Cache-Control'       => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ]);
        }
    }
    return redirect()->away('https://github.com/MohammadKevin/Nitipdong/releases/download/latest/NitipDong-latest.apk', 302);
})->name('app.download.android');

// Direct iOS Download Route
Route::get('/download/ios', function () {
    $version = env('APP_MOBILE_LATEST_VERSION', '2.5.1');
    $paths = [
        public_path('downloads/nitipdong.ipa'),
        public_path('downloads/NitipDong-latest.ipa'),
        base_path('../public_html/downloads/nitipdong.ipa'),
        base_path('../public_html/downloads/NitipDong-latest.ipa'),
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            return response()->download($p, "NitipDong-v{$version}.ipa", [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => "attachment; filename=\"NitipDong-v{$version}.ipa\"",
            ]);
        }
    }
    return redirect()->away('https://github.com/MohammadKevin/Nitipdong/releases/download/latest/NitipDong-latest.ipa', 302);
})->name('app.download.ios');

// App Landing Page & Download Hub
Route::get('/apps', function () {
    return view('app-download');
})->name('app.landing');

// Public Home
Route::get('/', function () {
    $products = Product::with(['store', 'category'])
        ->where('is_active', true)
        ->whereHas('store', function ($q) {
            $q->where('status', 'approved');
        })
        ->latest()
        ->take(24)
        ->get();

    $topProducts = Product::with(['store', 'category'])
        ->where('is_active', true)
        ->whereHas('store', function ($q) {
            $q->where('status', 'approved');
        })
        ->orderByDesc('sold_count')
        ->orderByDesc('rating')
        ->take(24)
        ->get();

    $officialProducts = Product::with(['store', 'category'])
        ->where('is_active', true)
        ->whereHas('store', function ($q) {
            $q->where('status', 'approved');
        })
        ->where(function ($q) {
            $q->where('is_featured', true)
              ->orWhereNotNull('discount_percentage');
        })
        ->latest()
        ->take(24)
        ->get();

    if ($officialProducts->isEmpty()) {
        $officialProducts = $products;
    }

    $categories = Category::withCount(['products' => function($q) {
        $q->where('is_active', true);
    }])->orderByDesc('products_count')->get();
    $activeFlashSale = FlashSale::active()->with(['items.product'])->first();
    $vouchers = Voucher::where('is_active', true)->latest()->take(6)->get();

    $officialStores = Store::where('status', 'approved')
        ->withCount('products')
        ->take(6)
        ->get();

    return view('welcome', compact('products', 'topProducts', 'officialProducts', 'categories', 'activeFlashSale', 'vouchers', 'officialStores'));
})->name('home');

// Public Product Catalog, Detail & Suggestions
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/api/search/suggestions', [PublicProductController::class, 'suggestions'])->name('api.search.suggestions');
Route::get('/product/{product}', [PublicProductController::class, 'show'])->name('product.show');

// Public Indonesian Administrative Regions & Geocoding API
Route::get('/api/regions/provinces', [App\Http\Controllers\Api\RegionController::class, 'provinces'])->name('api.regions.provinces');
Route::get('/api/regions/regencies/{province_id}', [App\Http\Controllers\Api\RegionController::class, 'regencies'])->name('api.regions.regencies');
Route::get('/api/regions/districts/{regency_id}', [App\Http\Controllers\Api\RegionController::class, 'districts'])->name('api.regions.districts');
Route::get('/api/regions/villages/{district_id}', [App\Http\Controllers\Api\RegionController::class, 'villages'])->name('api.regions.villages');
Route::get('/api/regions/reverse-geocode', [App\Http\Controllers\Api\RegionController::class, 'reverseGeocode'])->name('api.regions.reverse_geocode');

// Public Storefront (Etalase Toko)
Route::get('/toko/{store:slug}', [StoreFrontController::class, 'show'])->name('store.show');

// Dashboard Redirector (Global — available untuk semua konteks)
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }
    return match ($user->role) {
        'super_admin' => redirect()->route('super_admin.dashboard'),
        'admin'       => redirect()->route('admin.dashboard'),
        'seller'      => redirect()->route('seller.dashboard'),
        'courier'     => redirect()->route('courier.dashboard'),
        default       => redirect()->route('home'),
    };
})->name('dashboard');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/store/register', [StoreRegistrationController::class, 'create'])->middleware('verified')->name('store.register');
    Route::post('/store/register', [StoreRegistrationController::class, 'store'])->middleware('verified')->name('store.store');

    // In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    // Invoices, Shipping Labels & Live Maps Tracking
    Route::get('/orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');
    Route::get('/orders/{order}/shipping-label', [InvoiceController::class, 'shippingLabel'])->name('orders.shipping_label');
    Route::get('/orders/{order}/tracking', [OrderTrackingController::class, 'show'])->name('orders.tracking');

    // Product Discussions (Q&A)
    Route::post('/products/{product}/discussions', [ProductDiscussionController::class, 'store'])->name('products.discussions.store');
    Route::post('/products/{product}/discussions/{discussion}/reply', [ProductDiscussionController::class, 'reply'])->name('products.discussions.reply');
    Route::delete('/discussions/{discussion}', [ProductDiscussionController::class, 'destroy'])->name('products.discussions.destroy');

    // Super Admin Routes
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [SuperAdminController::class, 'chartData'])->name('dashboard.chart-data');
        // Users Management
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::post('/users/{user}/toggle-ban', [SuperAdminController::class, 'toggleBanUser'])->name('users.toggle_ban');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/stores', [SuperAdminController::class, 'stores'])->name('stores.index');
        Route::post('/stores/{store}/toggle-ban', [SuperAdminController::class, 'toggleBan'])->name('stores.toggle_ban');

        // Store Approvals
        Route::get('/store-approvals', [StoreApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/store-approvals/{store}/approve', [StoreApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/store-approvals/{store}/reject', [StoreApprovalController::class, 'reject'])->name('approvals.reject');

        // Product Moderation
        Route::get('/products', [ProductModerationController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/toggle-status', [ProductModerationController::class, 'toggleStatus'])->name('products.toggle_status');

        // Category Management
        Route::resource('categories', CategoryController::class)->names('categories');

        // Flash Sales Platform
        Route::resource('flash-sales', FlashSaleController::class)->names('flash_sales');
        Route::patch('/flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggle'])->name('flash_sales.toggle');
        Route::post('/flash-sales/{flashSale}/items', [FlashSaleController::class, 'addItem'])->name('flash_sales.items.add');
        Route::delete('/flash-sales/{flashSale}/items/{item}', [FlashSaleController::class, 'removeItem'])->name('flash_sales.items.remove');

        // Voucher Platform Management
        Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->names('vouchers');
        Route::patch('/vouchers/{voucher}/toggle', [\App\Http\Controllers\Admin\VoucherController::class, 'toggle'])->name('vouchers.toggle');

        // NDX Warehouses Logistics Management
        Route::resource('warehouses', WarehouseController::class)->names('warehouses');
        Route::patch('/warehouses/{warehouse}/toggle', [WarehouseController::class, 'toggle'])->name('warehouses.toggle');

        // Manage Operational Admins
        Route::resource('admins', AdminManagementController::class)->names('admins');

        // Withdrawals / Payouts
        Route::get('/withdrawals', [SuperAdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [SuperAdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject', [SuperAdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // Financial & Platform Reports
        Route::get('/reports', [ExportController::class, 'superAdminReportsPage'])->name('reports.index');
        Route::get('/reports/revenue/export', [ExportController::class, 'superAdminRevenueReport'])->name('reports.revenue.export');
        Route::get('/reports/print', [ExportController::class, 'superAdminPrintReport'])->name('reports.print');
    });

    // Admin & Super Admin Routes
    Route::middleware(['role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [StoreApprovalController::class, 'index'])->name('dashboard');
        Route::post('/stores/{store}/approve', [StoreApprovalController::class, 'approve'])->name('stores.approve');
        Route::post('/stores/{store}/reject', [StoreApprovalController::class, 'reject'])->name('stores.reject');

        Route::get('/products', [ProductModerationController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/toggle-status', [ProductModerationController::class, 'toggleStatus'])->name('products.toggle_status');

        // Dispute Resolution Center
        Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/complaints/{complaint}/resolve', [AdminComplaintController::class, 'resolve'])->name('complaints.resolve');

        // Real-Time Order Monitoring
        Route::get('/orders', [AdminOrderMonitoringController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/cancel', [AdminOrderMonitoringController::class, 'cancel'])->name('orders.cancel');

        Route::resource('categories', CategoryController::class);

        Route::resource('flash-sales', FlashSaleController::class)->names('flash_sales');
        Route::patch('/flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggle'])->name('flash_sales.toggle');
        Route::post('/flash-sales/{flashSale}/items', [FlashSaleController::class, 'addItem'])->name('flash_sales.items.add');
        Route::patch('/flash-sales/{flashSale}/items/{item}', [FlashSaleController::class, 'updateItem'])->name('flash_sales.items.update');
        Route::delete('/flash-sales/{flashSale}/items/{item}', [FlashSaleController::class, 'removeItem'])->name('flash_sales.items.remove');

        // Voucher Platform Management
        Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->names('vouchers');
        Route::patch('/vouchers/{voucher}/toggle', [\App\Http\Controllers\Admin\VoucherController::class, 'toggle'])->name('vouchers.toggle');

        // NDX Warehouses Logistics Management
        Route::resource('warehouses', WarehouseController::class)->names('warehouses');
        Route::patch('/warehouses/{warehouse}/toggle', [WarehouseController::class, 'toggle'])->name('warehouses.toggle');
    });

    // Seller Routes (Strictly requires verified email)
    Route::middleware(['role:seller', 'verified'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', function () {
            $store = Auth::user()->store;
            if (!$store || $store->status !== 'approved') {
                return redirect()->route('store.register');
            }
            $products = $store->products()->with('category')->latest()->get();
            $recentOrders = $store->orders()->with(['user', 'orderItems.product'])->latest()->take(5)->get();
            return view('seller.dashboard', compact('store', 'products', 'recentOrders'));
        })->name('dashboard');

        Route::resource('products', SellerProductController::class);

        // Store Settings, Bank Account & Locations
        Route::get('/settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [StoreSettingsController::class, 'update'])->name('settings.update');

        // Vouchers Management
        Route::resource('vouchers', SellerVoucherController::class)->names('vouchers');

        // Seller Wallet & Income Withdrawals
        Route::get('/wallet', [SellerWalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/withdraw', [SellerWalletController::class, 'withdraw'])->name('wallet.withdraw');

        // Order Management & Fulfillment
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/process', [OrderManagementController::class, 'processOrder'])->name('orders.process');
        Route::post('/orders/{order}/ship', [OrderManagementController::class, 'shipOrder'])->name('orders.ship');
        Route::post('/orders/{order}/cancel', [OrderManagementController::class, 'cancelOrder'])->name('orders.cancel');
        Route::match(['get', 'post', 'patch'], '/orders/{order}/update-status', [OrderManagementController::class, 'updateStatus'])->name('orders.update_status');

        // Reviews Management
        Route::get('/reviews', [SellerReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/reply', [SellerReviewController::class, 'reply'])->name('reviews.reply');

        // Complaints Handling
        Route::get('/complaints', [SellerComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/complaints/{complaint}/respond', [SellerComplaintController::class, 'respond'])->name('complaints.respond');

        // Export Sales Report
        Route::get('/reports/sales/export', [ExportController::class, 'sellerSalesReport'])->name('reports.sales.export');

        // Seller Chat Routes (Customer & Admin)
        Route::get('/chat/cus', [ChatController::class, 'sellerCustomerChat'])->name('chat.cus');
        Route::get('/chat/cus/{conversation}', [ChatController::class, 'show'])->name('chat.cus.show');
        Route::get('/chat/admin', [ChatController::class, 'sellerAdminChat'])->name('chat.admin');
        Route::get('/chat/admin/{conversation}', [ChatController::class, 'show'])->name('chat.admin.show');
    });

    // Customer Specific Routes (Strictly requires verified email)
    Route::middleware(['role:customer', 'verified'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            $userStore = Auth::user()->store;
            $orders = Auth::user()->orders()->with(['store', 'orderItems.product.reviews', 'reviews', 'complaint'])->latest()->get();
            $recommendedProducts = Product::with(['store', 'category'])
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->whereHas('store', function ($q) {
                    $q->where('status', 'approved');
                })
                ->inRandomOrder()
                ->take(8)
                ->get();
            $activeFlashSale = FlashSale::active()->with(['items.product'])->first();
            return view('customer.dashboard', compact('userStore', 'orders', 'recommendedProducts', 'activeFlashSale'));
        })->name('dashboard');

        Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
        Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');
    });

    // Cart, Checkout, Orders, Complaints, Reviews, Wishlist & Addresses (Strictly requires verified email)
    Route::middleware(['verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::get('/cart/items', [CartController::class, 'getItems'])->name('cart.items');
        Route::post('/cart/add/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

        // Voucher Routes
        Route::get('/vouchers', [CartController::class, 'vouchers'])->name('vouchers.index');
        Route::post('/vouchers/{voucher}/select', [CartController::class, 'selectVoucher'])->name('vouchers.select');
        Route::post('/cart/voucher/apply', [CartController::class, 'applyVoucher'])->name('cart.voucher.apply');
        Route::delete('/cart/voucher/remove', [CartController::class, 'removeVoucher'])->name('cart.voucher.remove');

        Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
        Route::post('/checkout', [OrderController::class, 'store'])->middleware('throttle:order-checkout')->name('order.store');
        Route::post('/shipping/calculate-options', [OrderController::class, 'calculateShippingOptions'])->name('shipping.calculate_options');

        Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
        Route::post('/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');
        Route::post('/orders/{order}/change-payment-method', [OrderController::class, 'changePaymentMethod'])->name('order.change_payment_method');
        Route::match(['get', 'post'], '/orders/{order}/midtrans/snap-token', [MidtransPaymentController::class, 'getSnapToken'])->name('order.midtrans_snap_token');
        Route::post('/orders/{order}/simulate-payment', [PaymentCallbackController::class, 'simulateInstantPayment'])->name('order.simulate_payment');
        Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('order.confirm_received');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

        // Complaints Route
        Route::post('/orders/{order}/complaints', [CustomerComplaintController::class, 'store'])->name('complaints.store');

        // Review Routes
        Route::post('/orders/{order}/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');

        // Wishlist Routes
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::get('/wishlist/items', [WishlistController::class, 'items'])->name('wishlist.items');
        Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

        // Address Routes
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set_default');
    });

    // Real-time Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/api/conversations', [ChatController::class, 'apiConversations'])->name('chat.api.conversations');
    Route::get('/chat/api/{conversation}/messages', [ChatController::class, 'apiMessages'])->name('chat.api.messages');
    Route::post('/chat/api/{conversation}/send', [ChatController::class, 'apiSendMessage'])->name('chat.api.send');
    Route::post('/chat/api/start/{receiver}', [ChatController::class, 'apiStartConversation'])->name('chat.api.start');
    Route::post('/chat/start/{receiver}', [ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // AI Assistant
    Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Secure Emergency/One-time Database Reset Endpoint
Route::get('/system-reset-database-2026', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'SaNitipdong2K26*') {
        abort(403, 'Akses Ditolak: Token otentikasi tidak valid.');
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        return response()->json([
            'success'     => true,
            'message'     => 'Database NitipDong berhasil di-reset bersih (migrate:fresh --seed). Akun Super Admin telah dibuat.',
            'super_admin' => [
                'name'  => 'Super Admin NitipDong',
                'email' => 'sanitipdong2026@gmail.com',
                'role'  => 'super_admin',
            ],
            'artisan_output' => $output,
        ]);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('System reset error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kegagalan saat eksekusi reset database.',
        ], 500);
    }
});
