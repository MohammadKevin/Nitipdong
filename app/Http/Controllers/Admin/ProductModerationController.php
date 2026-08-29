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

        if ($product->store && $product->store->user_id) {
            \App\Models\AppNotification::send(
                $product->store->user_id,
                $product->is_active ? 'Produk Diaktifkan Kembali' : 'Pemberitahuan Takedown Produk',
                $product->is_active 
                    ? "Produk '{$product->name}' Anda telah diaktifkan kembali oleh Admin."
                    : "Produk '{$product->name}' Anda dinonaktifkan (takedown) oleh Admin karena peninjauan kepatuhan.",
                'product',
                route('seller.products.index')
            );
        }

        return back()->with('success', "Produk '{$product->name}' berhasil {$status}.");
    }
}
