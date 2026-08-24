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
        $status = $request->query('status');

        $query = Product::with(['store.user', 'category']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'takedown') {
            $query->where('is_active', false);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $takedownProducts = Product::where('is_active', false)->count();

        return view('admin.products.index', compact(
            'products', 
            'search', 
            'status', 
            'totalProducts', 
            'activeProducts', 
            'takedownProducts'
        ));
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'diaktifkan kembali' : 'dinonaktifkan (takedown)';

        return back()->with('success', "Produk '{$product->name}' berhasil {$status}.");
    }
}
