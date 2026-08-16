<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    // Menampilkan halaman keranjang belanja
    public function index(): View
    {
        $carts = Cart::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        $totalPrice = $carts->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('customer.cart.index', compact('carts', 'totalPrice'));
    }

    // Menambah produk ke keranjang
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $request->input('quantity', 1);

        // Cek stok produk
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

    // Update jumlah produk di keranjang
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

    // Hapus produk dari keranjang
    public function destroy(Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }
}