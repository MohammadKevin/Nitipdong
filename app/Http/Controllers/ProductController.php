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

        $activeCategory = null;
        if ($catParam = $request->get('category')) {
            if (is_numeric($catParam)) {
                $activeCategory = Category::find((int) $catParam);
            } else {
                $activeCategory = Category::where('slug', $catParam)->first() 
                    ?: Category::findByObfuscatedId($catParam);
            }

            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
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

        return view('product.index', compact('products', 'categories', 'activeCategory'));
    }

    public function show(Product $product)
    {
        if (! $product->is_active || $product->store->status !== 'approved') {
            abort(404);
        }

        $product->load([
            'store',
            'category',
            'reviews' => fn ($q) => $q->with('user')->latest(),
            'discussions' => fn ($q) => $q->with(['user', 'replies.user'])->latest(),
        ]);

        $storeProducts = Product::where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('product.show', compact('product', 'storeProducts'));
    }

    public function suggestions(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['products' => [], 'stores' => [], 'categories' => []]);
        }

        $products = Product::with(['store'])
            ->where('is_active', true)
            ->whereHas('store', fn ($sq) => $sq->where('status', 'approved'))
            ->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            })
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id'                  => $p->id,
                'name'                => $p->name,
                'price'               => $p->final_price,
                'original_price'      => $p->price,
                'has_discount'        => $p->has_discount,
                'discount_percentage' => $p->discount_percentage,
                'image_url'           => $p->image_url,
                'store_name'          => $p->store->name ?? 'BelanjaIn',
                'url'                 => route('product.show', $p),
            ]);

        $stores = \App\Models\Store::where('status', 'approved')
            ->where('name', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(fn ($s) => [
                'id'       => $s->id,
                'name'     => $s->name,
                'logo_url' => $s->logo_url,
                'city'     => $s->city ?? 'Indonesia',
                'url'      => route('store.show', $s),
            ]);

        $categories = Category::where('name', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'url'  => route('products.index', ['category' => $c->slug]),
            ]);

        return response()->json([
            'products'   => $products,
            'stores'     => $stores,
            'categories' => $categories,
        ]);
    }
}
