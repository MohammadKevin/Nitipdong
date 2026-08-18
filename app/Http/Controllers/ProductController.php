<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category'])
            ->where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'approved'));

        if ($q = $request->get('q')) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        if ($min = $request->get('min_price')) {
            $query->where('price', '>=', $min);
        }
        if ($max = $request->get('max_price')) {
            $query->where('price', '<=', $max);
        }

        match ($request->get('sort', 'terbaru')) {
            'terlaris'   => $query->withCount('orderItems')->orderByDesc('order_items_count'),
            'harga_asc'  => $query->orderBy('price'),
            'harga_desc' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(24)->withQueryString();
        $categories = Category::all();

        $activeCategory = $categoryId
            ? Category::find($categoryId)
            : null;

        return view('product.index', compact('products', 'categories', 'activeCategory'));
    }

    public function show(Product $product)
    {
        if (! $product->is_active || $product->store->status !== 'approved') {
            abort(404);
        }

        $product->load(['store', 'category']);

        $storeProducts = Product::where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('product.show', compact('product', 'storeProducts'));
    }
}
