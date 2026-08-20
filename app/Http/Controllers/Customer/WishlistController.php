<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlists = Wishlist::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('customer.wishlist.index', compact('wishlists'));
    }

    public function items(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'success', 'count' => 0, 'items' => []]);
        }

        $wishlists = Wishlist::with(['product.store'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $items = $this->formatWishlistItems($wishlists);

        return response()->json([
            'status' => 'success',
            'count'  => $items->count(),
            'items'  => $items,
        ]);
    }

    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Produk dihapus dari wishlist.';
            $isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $status = 'added';
            $message = 'Produk ditambahkan ke wishlist.';
            $isWishlisted = true;
        }

        if ($request->wantsJson() || $request->ajax()) {
            $wishlists = Wishlist::with(['product.store'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            return response()->json([
                'status'        => $status,
                'message'       => $message,
                'is_wishlisted' => $isWishlisted,
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'total_count'   => $wishlists->count(),
                'items'         => $this->formatWishlistItems($wishlists),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Wishlist $wishlist): JsonResponse|RedirectResponse
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $wishlist->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $wishlists = Wishlist::with(['product.store'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Produk berhasil dihapus dari wishlist.',
                'total_count' => $wishlists->count(),
                'items'       => $this->formatWishlistItems($wishlists),
            ]);
        }

        return back()->with('success', 'Produk berhasil dihapus dari wishlist.');
    }

    private function formatWishlistItems($wishlists)
    {
        return $wishlists->map(function ($w) {
            $p = $w->product;
            if (!$p) return null;

            return [
                'id'             => $w->id,
                'product_id'     => $p->id,
                'name'           => $p->name,
                'price'          => (float) $p->final_price,
                'original_price' => (float) $p->price,
                'has_discount'   => (bool) $p->has_discount,
                'discount_percentage' => (int) $p->discount_percentage_effective,
                'image_url'      => $p->image_url ?? asset('img/saksershop-logo.png'),
                'product_url'    => route('product.show', $p),
                'store_name'     => $p->store->name ?? 'Official Store',
                'delete_url'     => route('customer.wishlist.destroy', $w),
                'cart_store_url' => route('customer.cart.store', $p),
            ];
        })->filter()->values();
    }
}
