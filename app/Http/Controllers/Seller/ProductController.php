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
    public function index(): View
    {
        $store = Auth::user()->store;

        $products = Product::with('category')
            ->where('store_id', $store?->id)
            ->latest()
            ->paginate(10);

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
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock'       => ['required', 'integer', 'min:0'],
            'badge'       => ['nullable', 'string', 'in:new,sale,hot,bestseller'],
            'is_featured' => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'additional_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $store = Auth::user()->store;

        // Upload main image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Upload additional images
        $additionalImages = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $additionalImages[] = $image->store('products', 'public');
            }
        }

        Product::create([
            'store_id'    => $store->id,
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'price'       => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'stock'       => $request->stock,
            'badge'       => $request->badge,
            'is_featured' => $request->boolean('is_featured', false),
            'image'       => $imagePath,
            'images'      => !empty($additionalImages) ? $additionalImages : null,
            'is_active'   => true,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan dengan ' . (count($additionalImages) + ($imagePath ? 1 : 0)) . ' foto!');
    }

    public function edit(Product $product): View
    {
        // Pastikan hanya pemilik produk yang bisa edit
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
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock'       => ['required', 'integer', 'min:0'],
            'badge'       => ['nullable', 'string', 'in:new,sale,hot,bestseller'],
            'is_featured' => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'additional_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
        ]);

        // Handle main image
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Handle additional images
        $existingImages = $product->images ?? [];

        // Remove selected images
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageToRemove) {
                if (in_array($imageToRemove, $existingImages) && Storage::disk('public')->exists($imageToRemove)) {
                    Storage::disk('public')->delete($imageToRemove);
                }
                $existingImages = array_filter($existingImages, fn($img) => $img !== $imageToRemove);
            }
        }

        // Add new additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $existingImages[] = $image->store('products', 'public');
            }
        }

        $product->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'stock'       => $request->stock,
            'badge'       => $request->badge,
            'is_featured' => $request->boolean('is_featured', false),
            'image'       => $imagePath,
            'images'      => !empty($existingImages) ? array_values($existingImages) : null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        // Delete main image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete additional images
        if ($product->images && is_array($product->images)) {
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
