<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreFrontController extends Controller
{
    public function show(Request $request, Store $store): View
    {
        if ($store->status !== 'approved') {
            abort(404, 'Toko ini sedang tidak aktif atau belum diverifikasi.');
        }

        $search = $request->query('q');
        $categorySlug = $request->query('kategori');
        $sort = $request->query('sort', 'latest');

        $productsQuery = Product::where('store_id', $store->id)
            ->where('is_active', true)
            ->with(['category', 'reviews']);

        if ($search) {
            $productsQuery->where('name', 'like', "%{$search}%");
        }

        if ($categorySlug) {
            $productsQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        match ($sort) {
            'price_asc'  => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'popular'    => $productsQuery->withCount('orderItems')->orderBy('order_items_count', 'desc'),
            default      => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(16)->withQueryString();

        $activeVouchers = $store->vouchers()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->take(6)
            ->get();

        $storeCategories = Category::whereHas('products', function ($q) use ($store) {
            $q->where('store_id', $store->id)->where('is_active', true);
        })->get();

        $totalCompletedOrders = Order::where('store_id', $store->id)
            ->where('status', 'completed')
            ->count();

        $storeReviews = Review::whereHas('product', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->with(['user', 'product'])->latest()->take(10)->get();

        $avgRating = Review::whereHas('product', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->avg('rating') ?: 5.0;

        $totalReviewsCount = Review::whereHas('product', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->count();

        return view('store.show', compact(
            'store',
            'products',
            'activeVouchers',
            'storeCategories',
            'totalCompletedOrders',
            'storeReviews',
            'avgRating',
            'totalReviewsCount',
            'search',
            'categorySlug',
            'sort'
        ));
    }
}
