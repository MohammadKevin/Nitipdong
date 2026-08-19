<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $store = Auth::user()->store;

        $query = Product::with('category')
            ->where('store_id', $store?->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:99'],
            'stock'               => ['required', 'integer', 'min:0'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'images.*'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.dashboard')->with('error', 'Toko belum terdaftar.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $extraImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $extraImages[] = $img->store('products', 'public');
            }
        }

        Product::create([
            'store_id'            => $store->id,
            'category_id'         => $request->category_id,
            'name'                => $request->name,
            'slug'                => Str::slug($request->name) . '-' . Str::random(5),
            'description'         => $request->description,
            'price'               => $request->price,
            'discount_percentage' => (int) $request->input('discount_percentage', 0),
            'stock'               => $request->stock,
            'image'               => $imagePath,
            'images'              => $extraImages ?: null,
            'is_active'           => true,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product): View
    {
        if ($product->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        $categories = Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($product->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:99'],
            'stock'               => ['required', 'integer', 'min:0'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'images.*'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'delete_images'       => ['nullable', 'array'],
            'delete_images.*'     => ['nullable', 'string'],
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Handle extra images: delete removed ones, keep existing, add new
        $existingImages = $product->images ?? [];

        // Delete images that were marked for removal
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
                $existingImages = array_values(array_filter($existingImages, fn($p) => $p !== $pathToDelete));
            }
        }

        // Add newly uploaded images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $existingImages[] = $img->store('products', 'public');
            }
        }

        $product->update([
            'category_id'         => $request->category_id,
            'name'                => $request->name,
            'description'         => $request->description,
            'price'               => $request->price,
            'discount_percentage' => (int) $request->input('discount_percentage', 0),
            'stock'               => $request->stock,
            'image'               => $imagePath,
            'images'              => count($existingImages) ? array_values($existingImages) : null,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('product.show', $product);
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->images ?? [] as $extraImage) {
            if (Storage::disk('public')->exists($extraImage)) {
                Storage::disk('public')->delete($extraImage);
            }
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
