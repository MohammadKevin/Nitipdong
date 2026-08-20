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
            return $item->product->price * $item->quantity;
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

        return view('customer.cart.index', compact(
            'carts',
            'itemsTotal',
            'subtotal',
            'productSavings',
            'voucherDiscount',
            'appliedVoucher',
            'finalTotal',
            'availableVouchers'
        ));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $request->input('quantity', 1);

        if ($product->stock < $quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $quantity);
        } else {
            Cart::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
        }

        return redirect()->route('customer.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($cart->product->stock < $request->quantity) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Jumlah barang berhasil diperbarui.');
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

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

        $source = $voucher->is_store_voucher ? ('Toko ' . ($voucher->store->name ?? '')) : 'BelanjaIn';
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
