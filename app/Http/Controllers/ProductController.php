<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // Pastikan produk aktif dan toko disetujui
        if (!$product->is_active || $product->store->status !== 'approved') {
            abort(404);
        }

        $product->load(['store', 'category']);

        // Ambil produk rekomendasi dari toko yang sama
        $storeProducts = Product::where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(6)
            ->get();

        return view('product.show', compact('product', 'storeProducts'));
    }
}
