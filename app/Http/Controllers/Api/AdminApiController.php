<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    // Check if user is admin
    private function checkAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return false;
        }
        return true;
    }

    /**
     * Get dashboard stats
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $pendingStoresCount = Store::where('status', 'pending')->count();
        $totalProductsCount = Product::count();
        $totalCategoriesCount = Category::count();
        $activeFlashSalesCount = FlashSale::active()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_stores' => $pendingStoresCount,
                'total_products' => $totalProductsCount,
                'total_categories' => $totalCategoriesCount,
                'active_flash_sales' => $activeFlashSalesCount,
            ]
        ]);
    }

    /**
     * Get pending stores
     */
    public function getPendingStores(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $stores = Store::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    /**
     * Approve store
     */
    public function approveStore(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $store = Store::findOrFail($id);
        
        DB::transaction(function () use ($store) {
            $store->update(['status' => 'approved']);
            $store->user->update(['role' => 'seller']);
        });

        return response()->json([
            'success' => true,
            'message' => "Toko {$store->name} berhasil disetujui."
        ]);
    }

    /**
     * Reject store
     */
    public function rejectStore(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $store = Store::findOrFail($id);
        $store->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => "Pengajuan toko {$store->name} ditolak."
        ]);
    }

    /**
     * Get all products for moderation
     */
    public function getProducts(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $products = Product::with(['store', 'category'])
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'obfuscated_id' => $product->obfuscated_id,
                    'name' => $product->name,
                    'price' => (double)$product->price,
                    'stock' => (int)$product->stock,
                    'is_active' => (bool)$product->is_active,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'store_name' => $product->store->name ?? 'Toko',
                    'category_name' => $product->category->name ?? 'Kategori',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Toggle product status
     */
    public function toggleProductStatus(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $product = Product::findOrFail($id);
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Produk '{$product->name}' berhasil {$status}.",
            'is_active' => (bool)$product->is_active
        ]);
    }

    /**
     * Get categories
     */
    public function getCategories(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $categories = Category::withCount('products')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Create category
     */
    public function storeCategory(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori baru berhasil ditambahkan.',
            'data' => $category
        ]);
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category
        ]);
    }

    /**
     * Delete category
     */
    public function deleteCategory(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori '{$category->name}' masih digunakan oleh {$category->products()->count()} produk."
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Kategori '{$category->name}' berhasil dihapus."
        ]);
    }

    /**
     * Get Flash Sales
     */
    public function getFlashSales(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $flashSales = FlashSale::with(['items.product.store'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $flashSales
        ]);
    }

    /**
     * Create Flash Sale
     */
    public function storeFlashSale(Request $request): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
        ]);

        $flashSale = FlashSale::create([
            'title'      => $validated['title'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'is_active'  => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Flash Sale '{$flashSale->title}' berhasil dibuat.",
            'data' => $flashSale
        ]);
    }

    /**
     * Toggle Flash Sale state
     */
    public function toggleFlashSale(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update([
            'is_active' => !$flashSale->is_active
        ]);

        $status = $flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Flash Sale '{$flashSale->title}' berhasil {$status}."
        ]);
    }

    /**
     * Add product to flash sale
     */
    public function addFlashSaleItem(Request $request, $id): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $flashSale = FlashSale::findOrFail($id);

        $validated = $request->validate([
            'product_id'       => ['required', 'exists:products,id'],
            'flash_sale_price' => ['required', 'numeric', 'min:1'],
            'stock_allocated'  => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['flash_sale_price'] >= $product->price) {
            return response()->json([
                'success' => false,
                'message' => 'Harga Flash Sale harus lebih rendah dari harga normal produk.'
            ], 422);
        }

        $discountPct = (int) round((($product->price - $validated['flash_sale_price']) / $product->price) * 100);

        $item = FlashSaleItem::updateOrCreate(
            [
                'flash_sale_id' => $flashSale->id,
                'product_id'    => $product->id,
            ],
            [
                'flash_sale_price'    => $validated['flash_sale_price'],
                'discount_percentage' => max(1, $discountPct),
                'stock_allocated'     => $validated['stock_allocated'],
                'is_active'           => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Produk '{$product->name}' berhasil ditambahkan ke Flash Sale.",
            'data' => $item
        ]);
    }

    /**
     * Remove product from flash sale
     */
    public function removeFlashSaleItem(Request $request, $id, $itemId): JsonResponse
    {
        if (!$this->checkAdmin($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $item = FlashSaleItem::where('flash_sale_id', $id)->where('id', $itemId)->firstOrFail();
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari Flash Sale.'
        ]);
    }
}
