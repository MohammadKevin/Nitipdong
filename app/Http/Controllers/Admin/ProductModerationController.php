<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductModerationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Product::with(['store.user', 'category'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('store', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function toggleStatus(Product $product)
    {
        // Toggle the is_active status
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'diaktifkan kembali' : 'dinonaktifkan (takedown)';

        return back()->with('success', "Produk '{$product->name}' berhasil {$status}.");
    }
}
