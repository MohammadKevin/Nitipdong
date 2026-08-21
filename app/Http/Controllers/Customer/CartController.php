<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $carts = Cart::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        $itemsTotal = $carts->sum(function ($item) {
            return $item->product->customer_base_price * $item->quantity;
        });

        $subtotal = $carts->sum(function ($item) {
            return $item->product->final_price * $item->quantity;
        });

        $productSavings = $itemsTotal - $subtotal;

        $voucherDiscount = 0;
        $appliedVoucher = null;

        if (session()->has('applied_voucher')) {
            $voucherCode = session('applied_voucher');
            $appliedVoucher = Voucher::active()->with('store')->where('code', $voucherCode)->first();

            if ($appliedVoucher) {
                if ($appliedVoucher->is_store_voucher) {
                    $storeItems = $carts->filter(fn ($item) => $item->product && $item->product->store_id == $appliedVoucher->store_id);
                    $applicableSubtotal = $storeItems->sum(fn ($item) => $item->product->final_price * $item->quantity);
                } else {
                    $applicableSubtotal = $subtotal;
                }

                $validation = $appliedVoucher->validateForSubtotal($applicableSubtotal);
                if ($validation['valid'] && $applicableSubtotal > 0) {
                    $voucherDiscount = $appliedVoucher->calculateDiscount($applicableSubtotal);
                } else {
                    session()->forget('applied_voucher');
                    session()->flash('info', 'Voucher ' . $voucherCode . ' dilepas: ' . $validation['message']);
                    $appliedVoucher = null;
                }
            } else {
                session()->forget('applied_voucher');
            }
        }

        $finalTotal = max(0, $subtotal - $voucherDiscount);

        $cartStoreIds = $carts->pluck('product.store_id')->filter()->unique()->toArray();

        $availableVouchers = Voucher::active()
            ->with('store')
            ->where(function ($q) use ($cartStoreIds) {
                $q->whereNull('store_id')
                  ->orWhereIn('store_id', $cartStoreIds);
            })
            ->take(6)
            ->get();

        $recommendedProducts = Product::with(['store', 'category'])
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('customer.cart.index', compact(
            'carts',
            'itemsTotal',
            'subtotal',
            'productSavings',
            'voucherDiscount',
            'appliedVoucher',
            'finalTotal',
            'availableVouchers',
            'recommendedProducts'
        ));
    }

    public function store(Request $request, Product $product): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if (! Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status'    => 'unauthenticated',
                    'message'   => 'Silakan masuk ke akun Anda terlebih dahulu untuk menambah barang ke keranjang.',
                    'login_url' => route('login'),
                ], 401);
            }
            return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk menambah produk ke keranjang.');
        }

        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'variant'  => ['nullable', 'string', 'max:100'],
        ]);

        $quantity = (int) $request->input('quantity', 1);
        $variant = $request->filled('variant') ? trim($request->input('variant')) : null;

        if ($product->stock < $quantity) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stok produk tidak mencukupi. Sisa stok: ' . $product->stock,
                ], 422);
            }
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cartQuery = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id);

        if ($variant) {
            $cartQuery->where('variant', $variant);
        } else {
            $cartQuery->whereNull('variant');
        }

        $cart = $cartQuery->first();

        if ($cart) {
            $cart->increment('quantity', $quantity);
        } else {
            Cart::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'variant'    => $variant,
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => 'Produk berhasil dimasukkan ke keranjang!',
                'cart_count' => $cartCount,
                'quantity'   => $quantity,
                'variant'    => $variant,
                'product'    => [
                    'name'      => $product->name,
                    'price'     => $product->final_price,
                    'image_url' => $product->image_url,
                ]
            ]);
        }

        if ($request->input('action') === 'buy') {
            return redirect()->route('customer.cart.index');
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function getItems(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['items' => [], 'count' => 0, 'subtotal' => 0, 'formatted_subtotal' => 'Rp 0']);
        }

        $carts = Cart::with(['product.store'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $items = $carts->map(fn ($c) => [
            'id'              => $c->id,
            'obfuscated_id'   => $c->obfuscated_id,
            'product_id'      => $c->product_id,
            'name'            => $c->product?->name ?? 'Produk',
            'price'           => $c->product ? $c->product->final_price : 0,
            'formatted_price' => 'Rp ' . number_format($c->product ? $c->product->final_price : 0, 0, ',', '.'),
            'image_url'       => $c->product?->image_url ?? asset('img/saksershop-logo.png'),
            'product_url'     => $c->product ? route('product.show', $c->product) : '#',
            'quantity'        => $c->quantity,
            'stock'           => $c->product?->stock ?? 99,
            'variant'         => $c->variant,
            'store_name'      => $c->product?->store?->name ?? 'SakserShop',
            'subtotal'        => ($c->product ? $c->product->final_price : 0) * $c->quantity,
            'formatted_subtotal' => 'Rp ' . number_format(($c->product ? $c->product->final_price : 0) * $c->quantity, 0, ',', '.'),
            'update_url'      => route('customer.cart.update', $c),
            'delete_url'      => route('customer.cart.destroy', $c),
        ]);

        $subtotal = $items->sum('subtotal');

        return response()->json([
            'status'             => 'success',
            'items'              => $items,
            'count'              => $carts->count(),
            'subtotal'           => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
        ]);
    }

    public function update(Request $request, Cart $cart): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($cart->product && $cart->product->stock < $request->quantity) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Jumlah melebihi stok yang tersedia (sisa: ' . $cart->product->stock . ')',
                ], 422);
            }
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cart->update(['quantity' => $request->quantity]);

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getItems($request);
        }

        return back()->with('success', 'Jumlah barang berhasil diperbarui.');
    }

    public function destroy(Request $request, Cart $cart): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getItems($request);
        }

        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    public function applyVoucher(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($request->code));
        $voucher = Voucher::active()->with('store')->where('code', $code)->first();

        if (!$voucher) {
            return back()->with('error', 'Kode voucher "' . $code . '" tidak ditemukan atau sudah tidak aktif.');
        }

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($voucher->is_store_voucher) {
            $storeItems = $carts->filter(fn ($i) => $i->product && $i->product->store_id == $voucher->store_id);
            if ($storeItems->isEmpty()) {
                $storeName = $voucher->store->name ?? 'toko ini';
                return back()->with('error', "Voucher {$voucher->code} khusus untuk produk dari toko {$storeName}.");
            }
            $applicableSubtotal = $storeItems->sum(fn ($i) => $i->product->final_price * $i->quantity);
        } else {
            $applicableSubtotal = $carts->sum(fn ($i) => $i->product->final_price * $i->quantity);
        }

        $validation = $voucher->validateForSubtotal($applicableSubtotal);
        if (!$validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        session(['applied_voucher' => $voucher->code]);

        $source = $voucher->is_store_voucher ? ('Toko ' . ($voucher->store->name ?? '')) : 'SakserShop';
        return back()->with('success', "Voucher {$source} ({$voucher->code}) berhasil digunakan!");
    }

    public function removeVoucher(): RedirectResponse
    {
        session()->forget('applied_voucher');

        return back()->with('success', 'Voucher promo berhasil dilepas.');
    }

    /**
     * Halaman daftar voucher customer (seperti Shopee "Voucher Saya")
     */
    public function vouchers(): View
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        $cartStoreIds = $carts->pluck('product.store_id')->filter()->unique()->toArray();
        $subtotal = $carts->sum(fn ($i) => $i->product->final_price * $i->quantity);

        // Get all available vouchers (platform + store vouchers)
        $vouchers = Voucher::active()
            ->with('store')
            ->where(function ($q) use ($cartStoreIds) {
                $q->whereNull('store_id') // Platform vouchers
                  ->orWhereIn('store_id', $cartStoreIds); // Store vouchers for items in cart
            })
            ->orderBy('amount', 'desc')
            ->get();

        $selectedVoucherCode = session('applied_voucher');

        return view('customer.vouchers.index', compact('vouchers', 'subtotal', 'selectedVoucherCode'));
    }

    /**
     * Select voucher (simpan ke session, tidak redirect ke cart)
     */
    public function selectVoucher(Request $request, Voucher $voucher): RedirectResponse
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($voucher->is_store_voucher) {
            $storeItems = $carts->filter(fn ($i) => $i->product && $i->product->store_id == $voucher->store_id);
            if ($storeItems->isEmpty()) {
                return back()->with('error', 'Voucher ini hanya untuk produk dari toko ' . ($voucher->store->name ?? 'tertentu') . '.');
            }
            $applicableSubtotal = $storeItems->sum(fn ($i) => $i->product->final_price * $i->quantity);
        } else {
            $applicableSubtotal = $carts->sum(fn ($i) => $i->product->final_price * $i->quantity);
        }

        $validation = $voucher->validateForSubtotal($applicableSubtotal);
        if (!$validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        // Simpan voucher ke session
        session(['applied_voucher' => $voucher->code]);

        return back()->with('success', 'Voucher ' . $voucher->code . ' dipilih! Silakan lanjut ke checkout.');
    }
}
