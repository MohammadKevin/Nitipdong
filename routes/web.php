<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\ProductModerationController;
use App\Http\Controllers\Admin\StoreApprovalController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\ComplaintController as CustomerComplaintController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\StoreRegistrationController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\DuitkuPaymentController;
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
Route::post('/api/duitku/callback', [DuitkuPaymentController::class, 'handleCallback'])->name('duitku.callback');

// Download Mobile Android APK Route
Route::get('/download/app', function () {
    $apkPath = public_path('downloads/nitipdong.apk');
    if (file_exists($apkPath)) {
        return response()->download($apkPath, 'NitipDong-v1.0.0.apk');
    }
    return redirect('https://github.com/MohammadKevin/Nitipdong/actions');
})->name('app.download');

// Return URL Redirect after Duitku Payment
Route::get('/payment/finish', [DuitkuPaymentController::class, 'handleReturn'])->name('duitku.return');

// Public Home
Route::get('/', function () {
    if (Auth::check() && !request()->has('is_from_login') && empty(request()->query())) {
        return redirect('/?is_from_login=true');
    }

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

    $categories = Category::all();
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

// Public Storefront (Etalase Toko)
Route::get('/toko/{store:slug}', [StoreFrontController::class, 'show'])->name('store.show');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
    Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');

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
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/stores', [SuperAdminController::class, 'stores'])->name('stores.index');
        Route::post('/stores/{store}/toggle-ban', [SuperAdminController::class, 'toggleBan'])->name('stores.toggle_ban');

        // Withdrawals / Payouts
        Route::get('/withdrawals', [SuperAdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [SuperAdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject', [SuperAdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // Reports Export
        Route::get('/reports/revenue/export', [ExportController::class, 'superAdminRevenueReport'])->name('reports.revenue.export');
    });

    // Admin & Super Admin Routes
    Route::middleware(['role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [StoreApprovalController::class, 'index'])->name('dashboard');
        Route::post('/stores/{store}/approve', [StoreApprovalController::class, 'approve'])->name('stores.approve');
        Route::post('/stores/{store}/reject', [StoreApprovalController::class, 'reject'])->name('stores.reject');

        Route::get('/products', [ProductModerationController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/toggle-status', [ProductModerationController::class, 'toggleStatus'])->name('products.toggle_status');

        Route::resource('categories', CategoryController::class);

        Route::resource('flash-sales', FlashSaleController::class)->names('flash_sales');
        Route::patch('/flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggle'])->name('flash_sales.toggle');
        Route::post('/flash-sales/{flashSale}/items', [FlashSaleController::class, 'addItem'])->name('flash_sales.items.add');
        Route::patch('/flash-sales/{flashSale}/items/{item}', [FlashSaleController::class, 'updateItem'])->name('flash_sales.items.update');
        Route::delete('/flash-sales/{flashSale}/items/{item}', [FlashSaleController::class, 'removeItem'])->name('flash_sales.items.remove');
    });

    // Seller Routes
    Route::middleware(['role:seller'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', function () {
            $store = Auth::user()->store;
            $products = Product::where('store_id', $store?->id)->latest()->get();
            $categories = Category::all();
            $orders = Order::where('store_id', $store?->id)->with(['user', 'orderItems.product'])->latest()->get();
            return view('seller.dashboard', compact('store', 'products', 'categories', 'orders'));
        })->name('dashboard');

        Route::post('/products/bulk-action', [SellerProductController::class, 'bulkAction'])->name('products.bulk_action');
        Route::resource('products', SellerProductController::class);

        Route::resource('vouchers', SellerVoucherController::class);
        Route::patch('/vouchers/{voucher}/toggle', [SellerVoucherController::class, 'toggle'])->name('vouchers.toggle');

        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update_status');

        Route::get('/reviews', [SellerReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/reply', [SellerReviewController::class, 'reply'])->name('reviews.reply');

        // Wallet & Withdrawals
        Route::get('/wallet', [SellerWalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/withdraw', [SellerWalletController::class, 'withdraw'])->name('wallet.withdraw');

        // Seller Store Profile & Shipping Address Settings
        Route::get('/settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [StoreSettingsController::class, 'update'])->name('settings.update');

        // Complaints & Returns
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

    // Customer Specific Routes
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
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
            return view('customer.dashboard', compact('userStore', 'orders', 'recommendedProducts'));
        })->name('dashboard');

        Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
        Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');
    });

    // Cart, Checkout, Orders, Complaints, Reviews, Wishlist & Addresses
    Route::prefix('customer')->name('customer.')->group(function () {
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
        Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
        Route::post('/shipping/calculate-options', [OrderController::class, 'calculateShippingOptions'])->name('shipping.calculate_options');

        Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
        Route::post('/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');
        Route::match(['get', 'post'], '/orders/{order}/midtrans/snap-token', [MidtransPaymentController::class, 'getSnapToken'])->name('order.midtrans_snap_token');
        Route::match(['get', 'post'], '/orders/{order}/duitku/create', [MidtransPaymentController::class, 'getSnapToken'])->name('order.duitku_create');
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
