<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get all cart items for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $carts = $request->user()->carts()->with(['product.store'])->latest()->get();

        $subtotal = $carts->sum(function ($cart) {
            return ($cart->product ? $cart->product->final_price : 0) * $cart->quantity;
        });

        $items = $carts->map(function ($cart) {
            $p = $cart->product;
            if (!$p) return null;

            return [
                'id'            => $cart->id,
                'product_id'    => $p->id,
                'name'          => $p->name,
                'image_url'     => $p->image_url ?? asset('img/saksershop-logo.png'),
                'price'         => (float) $p->final_price,
                'original_price'=> (float) $p->price,
                'has_discount'  => (bool) $p->has_discount,
                'quantity'      => (int) $cart->quantity,
                'variant'       => $cart->variant,
                'stock'         => (int) $p->stock,
                'subtotal'      => (float) ($p->final_price * $cart->quantity),
                'store_name'    => $p->store->name ?? 'NitipDong Official',
            ];
        })->filter()->values();

        return response()->json([
            'success'   => true,
            'items'     => $items,
            'item_count'=> $items->count(),
            'total_qty' => $items->sum('quantity'),
            'subtotal'  => (float) $subtotal,
        ]);
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'variant'    => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('variant', $request->variant)
            ->first();

        if ($cart) {
            $cart->quantity += $request->quantity;
            if ($cart->quantity > $product->stock) {
                $cart->quantity = $product->stock;
            }
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'quantity'   => min($request->quantity, $product->stock),
                'variant'    => $request->variant,
            ]);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Produk berhasil ditambahkan ke keranjang.',
            'cart_count' => $user->carts()->count(),
        ]);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->user()->carts()->findOrFail($id);
        $product = $cart->product;

        $qty = min($request->quantity, $product ? $product->stock : $request->quantity);
        $cart->update(['quantity' => $qty]);

        return response()->json([
            'success'  => true,
            'message'  => 'Jumlah barang berhasil diperbarui.',
            'quantity' => $cart->quantity,
            'subtotal' => (float) (($product ? $product->final_price : 0) * $cart->quantity),
        ]);
    }

    /**
     * Delete item from cart.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $cart = $request->user()->carts()->findOrFail($id);
        $cart->delete();

        return response()->json([
            'success'    => true,
            'message'    => 'Barang berhasil dihapus dari keranjang.',
            'cart_count' => $request->user()->carts()->count(),
        ]);
    }

    /**
     * Get cart count badge.
     */
    public function count(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count'   => $request->user()->carts()->count(),
        ]);
    }
}
