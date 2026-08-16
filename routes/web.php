<?php

use App\Http\Controllers\Admin\StoreApprovalController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\StoreRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\OrderManagementController;
use App\Http\Controllers\Seller\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman Katalog Utama (Publik)
Route::get('/', function () {
    $products = Product::with(['store', 'category'])
        ->where('is_active', true)
        ->whereHas('store', function ($q) {
            $q->where('status', 'approved');
        })
        ->latest()
        ->take(8)
        ->get();
        
    $categories = Category::all();
    return view('welcome', compact('products', 'categories'));
});

// Area yang Memerlukan Login
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Super Admin
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [\App\Http\Controllers\SuperAdminController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/stores', [\App\Http\Controllers\SuperAdminController::class, 'stores'])->name('stores.index');
        Route::post('/stores/{store}/toggle-ban', [\App\Http\Controllers\SuperAdminController::class, 'toggleBan'])->name('stores.toggle_ban');
    });

    // 2. Admin (Moderasi Pengajuan Toko)
    Route::middleware(['role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [StoreApprovalController::class, 'index'])->name('dashboard');
        Route::post('/stores/{store}/approve', [StoreApprovalController::class, 'approve'])->name('stores.approve');
        Route::post('/stores/{store}/reject', [StoreApprovalController::class, 'reject'])->name('stores.reject');
    });

    // 3. Seller (Kelola Toko, Produk & Pesanan Masuk)
    Route::middleware(['role:seller'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', function () {
            $store = Auth::user()->store;
            $products = Product::where('store_id', $store?->id)->latest()->get();
            $categories = Category::all();
            return view('seller.dashboard', compact('store', 'products', 'categories'));
        })->name('dashboard');

        // CRUD Produk
        Route::resource('products', ProductController::class);

        // Manajemen Pesanan
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update_status');
    });

    // 4. Customer (Belanja, Cart, Checkout, Payment & Pengajuan Toko)
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            $userStore = Auth::user()->store;
            $orders = Auth::user()->orders()->with(['store', 'orderItems.product'])->latest()->get();
            return view('customer.dashboard', compact('userStore', 'orders'));
        })->name('dashboard');

        // Keranjang Belanja (Cart)
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

        // Checkout & Pembuatan Pesanan
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
        Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');

        // Pembayaran & Upload Bukti Transfer
        Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
        Route::post('/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');

        // Pengajuan Buka Toko Baru
        Route::get('/store/register', [StoreRegistrationController::class, 'create'])->name('store.register');
        Route::post('/store/register', [StoreRegistrationController::class, 'store'])->name('store.store');
    });

    // Modul Chat (Bisa Diakses Semua Role yang Login)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/start/{receiver}', [ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Asisten AI
    Route::post('/ai/chat', [\App\Http\Controllers\AiAssistantController::class, 'chat'])->name('ai.chat');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';    