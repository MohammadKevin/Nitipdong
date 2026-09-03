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

        if (!$store) {
            $products = collect();
            return view('seller.products.index', compact('products'));
        }

        $query = Product::with('category')
            ->where('store_id', $store->id);

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

        $products = $query->latest()->get();

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('store.register')->with('error', 'Silakan daftarkan toko Anda terlebih dahulu.');
        }

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
            'stock'               => ['required', 'integer', 'min:0', 'max:10000'],
            'max_order_quantity'  => ['nullable', 'integer', 'min:1', 'max:1000'],
            'weight'              => ['nullable', 'numeric', 'min:0'],
            'condition'           => ['nullable', 'in:new,used,baru,bekas,second'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'images'              => ['nullable', 'array', 'max:5'],
            'images.*'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'stock.max' => 'Jumlah stok tidak boleh melebihi batas maksimal 10.000 unit.',
            'stock.min' => 'Jumlah stok tidak boleh bernilai negatif.',
        ]);

        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.dashboard')->with('error', 'Toko belum terdaftar.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

        $extraImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $uploaded = $this->uploadImage($img);
                if ($uploaded) {
                    $extraImages[] = $uploaded;
                }
            }
        }

        // Build specifications array
        $specifications = [];
        if ($request->has('spec_keys') && $request->has('spec_values')) {
            $keys = $request->input('spec_keys', []);
            $values = $request->input('spec_values', []);
            foreach ($keys as $index => $key) {
                if (!empty($key) && !empty($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
        }

        // Build variants array
        $variants = [];
        if ($request->has('variant_names')) {
            $variantNames = $request->input('variant_names', []);
            foreach ($variantNames as $vIndex => $variantName) {
                if (!empty($variantName)) {
                    $optionsKey = "variant_{$vIndex}_options";
                    $options = $request->input($optionsKey, []);
                    $filteredOptions = array_filter($options, fn($opt) => !empty($opt));

                    if (count($filteredOptions) > 0) {
                        $variants[] = [
                            'name' => $variantName,
                            'options' => array_values($filteredOptions)
                        ];
                    }
                }
            }
        }

        $rawPrice = $request->input('price');
        $price = is_numeric($rawPrice) ? (float) $rawPrice : (float) preg_replace('/[^0-9.]/', '', (string) $rawPrice);

        $rawWeight = $request->input('weight');
        $weight = ($rawWeight !== null && $rawWeight !== '') ? (float) $rawWeight : null;

        $rawCondition = strtolower((string) $request->input('condition', 'new'));
        $condition = in_array($rawCondition, ['used', 'bekas', 'second']) ? 'used' : 'new';

        Product::create([
            'store_id'            => $store->id,
            'category_id'         => $request->category_id,
            'name'                => $request->name,
            'slug'                => Str::slug($request->name) . '-' . Str::random(5),
            'description'         => $request->description,
            'price'               => $price,
            'discount_percentage' => (int) $request->input('discount_percentage', 0),
            'stock'               => (int) $request->stock,
            'max_order_quantity'  => $request->filled('max_order_quantity') ? (int) $request->max_order_quantity : 10,
            'weight'              => $weight,
            'condition'           => $condition,
            'specifications'      => count($specifications) > 0 ? $specifications : null,
            'variants'            => count($variants) > 0 ? $variants : null,
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
            'stock'               => ['required', 'integer', 'min:0', 'max:10000'],
            'max_order_quantity'  => ['nullable', 'integer', 'min:1', 'max:1000'],
            'weight'              => ['nullable', 'numeric', 'min:0'],
            'condition'           => ['nullable', 'in:new,used,baru,bekas,second'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'images'              => ['nullable', 'array', 'max:5'],
            'images.*'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'delete_images'       => ['nullable', 'array'],
            'delete_images.*'     => ['nullable', 'string'],
        ], [
            'stock.max' => 'Jumlah stok tidak boleh melebihi batas maksimal 10.000 unit.',
            'stock.min' => 'Jumlah stok tidak boleh bernilai negatif.',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $this->deleteImage($imagePath);
            $imagePath = $this->uploadImage($request->file('image'));
        }

        $existingImages = $product->images ?? [];

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $pathToDelete) {
                $this->deleteImage($pathToDelete);
                $existingImages = array_values(array_filter($existingImages, fn($p) => $p !== $pathToDelete));
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $uploaded = $this->uploadImage($img);
                if ($uploaded) {
                    $existingImages[] = $uploaded;
                }
            }
        }

        // Build specifications array
        $specifications = [];
        if ($request->has('spec_keys') && $request->has('spec_values')) {
            $keys = $request->input('spec_keys', []);
            $values = $request->input('spec_values', []);
            foreach ($keys as $index => $key) {
                if (!empty($key) && !empty($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
        }

        // Build variants array
        $variants = [];
        if ($request->has('variant_names')) {
            $variantNames = $request->input('variant_names', []);
            foreach ($variantNames as $vIndex => $variantName) {
                if (!empty($variantName)) {
                    $optionsKey = "variant_{$vIndex}_options";
                    $options = $request->input($optionsKey, []);
                    $filteredOptions = array_filter($options, fn($opt) => !empty($opt));

                    if (count($filteredOptions) > 0) {
                        $variants[] = [
                            'name' => $variantName,
                            'options' => array_values($filteredOptions)
                        ];
                    }
                }
            }
        }

        $rawPrice = $request->input('price');
        $price = is_numeric($rawPrice) ? (float) $rawPrice : (float) preg_replace('/[^0-9.]/', '', (string) $rawPrice);

        $rawWeight = $request->input('weight');
        $weight = ($rawWeight !== null && $rawWeight !== '') ? (float) $rawWeight : $product->weight;

        $rawCondition = strtolower((string) $request->input('condition', $product->condition ?? 'new'));
        $condition = in_array($rawCondition, ['used', 'bekas', 'second']) ? 'used' : 'new';

        $product->update([
            'category_id'         => $request->category_id,
            'name'                => $request->name,
            'description'         => $request->description,
            'specifications'      => count($specifications) ? $specifications : null,
            'variants'            => count($variants) ? $variants : null,
            'price'               => $price,
            'discount_percentage' => (int) $request->input('discount_percentage', 0),
            'stock'               => (int) $request->stock,
            'max_order_quantity'  => $request->filled('max_order_quantity') ? (int) $request->max_order_quantity : 10,
            'weight'              => $weight,
            'condition'           => $condition,
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

        // Check if product has historical order items
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);
            return redirect()->route('seller.products.index')
                ->with('info', "Produk '{$product->name}' memiliki riwayat transaksi pembeli, sehingga dinonaktifkan dari etalase daripada dihapus permanen agar riwayat belanja tetap utuh.");
        }

        $this->deleteImage($product->image);

        foreach ($product->images ?? [] as $extraImage) {
            $this->deleteImage($extraImage);
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action'        => ['required', 'string', 'in:delete,activate,deactivate,set_discount'],
            'product_ids'   => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'exists:products,id'],
            'discount'      => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $store = Auth::user()->store;
        if (!$store) {
            return redirect()->route('seller.products.index')->with('error', 'Toko tidak ditemukan.');
        }

        $products = Product::where('store_id', $store->id)
            ->whereIn('id', $request->product_ids)
            ->get();

        $count = $products->count();
        if ($count === 0) {
            return redirect()->route('seller.products.index')->with('error', 'Tidak ada produk yang valid dipilih.');
        }

        switch ($request->action) {
            case 'delete':
                $deletedCount = 0;
                $deactivatedCount = 0;
                foreach ($products as $product) {
                    if ($product->orderItems()->exists()) {
                        $product->update(['is_active' => false]);
                        $deactivatedCount++;
                    } else {
                        $this->deleteImage($product->image);
                        foreach ($product->images ?? [] as $extraImage) {
                            $this->deleteImage($extraImage);
                        }
                        $product->delete();
                        $deletedCount++;
                    }
                }

                $msg = "Aksi selesai: {$deletedCount} produk dihapus permanen";
                if ($deactivatedCount > 0) {
                    $msg .= ", {$deactivatedCount} produk dinonaktifkan karena memiliki riwayat transaksi.";
                }
                return redirect()->route('seller.products.index')->with('success', $msg);

            case 'activate':
                Product::where('store_id', $store->id)
                    ->whereIn('id', $products->pluck('id'))
                    ->update(['is_active' => true]);
                return redirect()->route('seller.products.index')->with('success', "Berhasil mengaktifkan {$count} produk terpilih.");

            case 'deactivate':
                Product::where('store_id', $store->id)
                    ->whereIn('id', $products->pluck('id'))
                    ->update(['is_active' => false]);
                return redirect()->route('seller.products.index')->with('success', "Berhasil menonaktifkan {$count} produk terpilih.");

            case 'set_discount':
                $discount = (int) $request->input('discount', 0);
                Product::where('store_id', $store->id)
                    ->whereIn('id', $products->pluck('id'))
                    ->update(['discount_percentage' => $discount]);
                return redirect()->route('seller.products.index')->with('success', "Berhasil menerapkan diskon {$discount}% pada {$count} produk terpilih.");

            default:
                return redirect()->route('seller.products.index')->with('error', 'Aksi masal tidak dikenali.');
        }
    }

    /**
     * Helper to upload image to server storage.
     */
    protected function uploadImage($file): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        return $file->store('products', 'public');
    }

    /**
     * Helper to delete image from server storage.
     */
    protected function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://') && !str_starts_with($path, 'img/')) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
