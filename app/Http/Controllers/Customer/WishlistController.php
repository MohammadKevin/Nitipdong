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
            return response()->json([
                'status'        => $status,
                'message'       => $message,
                'is_wishlisted' => $isWishlisted,
                'total_count'   => Wishlist::where('user_id', $user->id)->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Wishlist $wishlist): RedirectResponse
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $wishlist->delete();

        return back()->with('success', 'Produk berhasil dihapus dari wishlist.');
    }
}
