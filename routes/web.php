<?php

use App\Http\Controllers\Admin\StoreApprovalController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\StoreRegistrationController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\OrderManagementController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ReviewController as SellerReviewController;
use App\Http\Controllers\Seller\VoucherController as SellerVoucherController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    $categories = Category::all();
    $activeFlashSale = \App\Models\FlashSale::active()->with(['items.product'])->first();
    $vouchers = \App\Models\Voucher::where('is_active', true)->latest()->take(6)->get();
    
    $officialStores = \App\Models\Store::where('status', 'approved')
        ->withCount('products')
        ->take(6)
        ->get();

    return view('welcome', compact('products', 'categories', 'activeFlashSale', 'vouchers', 'officialStores'));
})->name('home');

Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
    Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');

    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [\App\Http\Controllers\SuperAdminController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/stores', [\App\Http\Controllers\SuperAdminController::class, 'stores'])->name('stores.index');
        Route::post('/stores/{store}/toggle-ban', [\App\Http\Controllers\SuperAdminController::class, 'toggleBan'])->name('stores.toggle_ban');
    });

    Route::middleware(['role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [StoreApprovalController::class, 'index'])->name('dashboard');
        Route::post('/stores/{store}/approve', [StoreApprovalController::class, 'approve'])->name('stores.approve');
        Route::post('/stores/{store}/reject', [StoreApprovalController::class, 'reject'])->name('stores.reject');

        Route::get('/products', [\App\Http\Controllers\Admin\ProductModerationController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductModerationController::class, 'toggleStatus'])->name('products.toggle_status');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        Route::resource('flash-sales', \App\Http\Controllers\Admin\FlashSaleController::class)->names('flash_sales');
        Route::patch('/flash-sales/{flashSale}/toggle', [\App\Http\Controllers\Admin\FlashSaleController::class, 'toggle'])->name('flash_sales.toggle');
        Route::post('/flash-sales/{flashSale}/items', [\App\Http\Controllers\Admin\FlashSaleController::class, 'addItem'])->name('flash_sales.items.add');
        Route::patch('/flash-sales/{flashSale}/items/{item}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'updateItem'])->name('flash_sales.items.update');
        Route::delete('/flash-sales/{flashSale}/items/{item}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'removeItem'])->name('flash_sales.items.remove');
    });

    Route::middleware(['role:seller'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', function () {
            $store = Auth::user()->store;
            $products = Product::where('store_id', $store?->id)->latest()->get();
            $categories = Category::all();
            $orders = \App\Models\Order::where('store_id', $store?->id)->with(['user', 'orderItems.product'])->latest()->get();
            return view('seller.dashboard', compact('store', 'products', 'categories', 'orders'));
        })->name('dashboard');

        Route::resource('products', ProductController::class);

        Route::resource('vouchers', SellerVoucherController::class);
        Route::patch('/vouchers/{voucher}/toggle', [SellerVoucherController::class, 'toggle'])->name('vouchers.toggle');

        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update_status');

        Route::get('/reviews', [SellerReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/reply', [SellerReviewController::class, 'reply'])->name('reviews.reply');
    });

    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            $userStore = Auth::user()->store;
            $orders = Auth::user()->orders()->with(['store', 'orderItems.product.reviews', 'reviews'])->latest()->get();
            return view('customer.dashboard', compact('userStore', 'orders'));
        })->name('dashboard');

        Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
        Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');
    });

    // Cart, Checkout, Orders, Reviews, Wishlist & Addresses accessible by all authenticated customers
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::post('/cart/voucher/apply', [CartController::class, 'applyVoucher'])->name('cart.voucher.apply');
        Route::delete('/cart/voucher/remove', [CartController::class, 'removeVoucher'])->name('cart.voucher.remove');

        Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
        Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');

        Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
        Route::post('/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');
        Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('order.confirm_received');

        // Review Routes
        Route::post('/orders/{order}/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');

        // Wishlist Routes
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

        // Address Routes
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set_default');
    });

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/start/{receiver}', [ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    Route::post('/ai/chat', [\App\Http\Controllers\AiAssistantController::class, 'chat'])->name('ai.chat');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
