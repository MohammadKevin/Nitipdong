<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = FlashSale::withCount('items')->latest();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status === 'running') {
            $query->active();
        } elseif ($status === 'upcoming') {
            $query->upcoming();
        } elseif ($status === 'ended') {
            $query->ended();
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $flashSales = $query->paginate(10)->withQueryString();

        $runningCount = FlashSale::active()->count();
        $upcomingCount = FlashSale::upcoming()->count();
        $totalEvents = FlashSale::count();

        return view('admin.flash_sales.index', compact('flashSales', 'status', 'search', 'runningCount', 'upcomingCount', 'totalEvents'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'approved'))
            ->with(['store', 'category'])
            ->get();

        return view('admin.flash_sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
            'is_active'  => ['nullable', 'boolean'],
            'banner'     => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $flashSale = FlashSale::create($validated);

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['product_id']) && !empty($itemData['flash_sale_price'])) {
                    $productId = $itemData['product_id'];
                    $product = is_numeric($productId) 
                        ? Product::find((int) $productId) 
                        : Product::findByObfuscatedId($productId);

                    if ($product) {
                        $price = (float) $itemData['flash_sale_price'];
                        $stock = (int) ($itemData['stock_allocated'] ?? 10);
                        $discountPct = $product->price > 0 ? (int) round((($product->price - $price) / $product->price) * 100) : 0;

                        FlashSaleItem::create([
                            'flash_sale_id'       => $flashSale->id,
                            'product_id'          => $product->id,
                            'flash_sale_price'    => $price,
                            'discount_percentage' => max(1, $discountPct),
                            'stock_allocated'     => max(1, $stock),
                            'stock_sold'          => 0,
                            'is_active'           => true,
                        ]);
                    }
                }
            }
        }

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.flash_sales.show' : 'admin.flash_sales.show';
        return redirect()->route($route, $flashSale)
            ->with('success', "Flash Sale '{$flashSale->title}' berhasil dibuat! Silakan kelola produk yang diikutsertakan.");
    }

    public function show(FlashSale $flashSale, Request $request): View
    {
        $flashSale->load(['items.product.store', 'items.product.category']);

        $enrolledProductIds = $flashSale->items->pluck('product_id')->toArray();

        $availableProducts = Product::where('is_active', true)
            ->whereHas('store', fn ($q) => $q->where('status', 'approved'))
            ->whereNotIn('id', $enrolledProductIds)
            ->with(['store', 'category'])
            ->get();

        return view('admin.flash_sales.show', compact('flashSale', 'availableProducts'));
    }

    public function edit(FlashSale $flashSale): View
    {
        return view('admin.flash_sales.edit', compact('flashSale'));
    }

    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
            'is_active'  => ['nullable', 'boolean'],
            'banner'     => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $flashSale->update($validated);

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.flash_sales.show' : 'admin.flash_sales.show';
        return redirect()->route($route, $flashSale)
            ->with('success', "Informasi Flash Sale '{$flashSale->title}' berhasil diperbarui.");
    }

    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $title = $flashSale->title;
        $flashSale->delete();

        $route = auth()->user()?->role === 'super_admin' ? 'super_admin.flash_sales.index' : 'admin.flash_sales.index';
        return redirect()->route($route)
            ->with('success', "Flash Sale '{$title}' berhasil dihapus.");
    }

    public function toggle(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->update([
            'is_active' => !$flashSale->is_active
        ]);

        $status = $flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Flash Sale '{$flashSale->title}' berhasil {$status}.");
    }

    public function addItem(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $request->validate([
            'product_id'          => ['required'],
            'flash_sale_price'    => ['required', 'numeric', 'min:1'],
            'stock_allocated'     => ['required', 'integer', 'min:1'],
        ]);

        $productId = $request->product_id;
        $product = is_numeric($productId)
            ? Product::findOrFail((int) $productId)
            : Product::findByObfuscatedIdOrFail($productId);

        if ($request->flash_sale_price >= $product->price) {
            return back()->with('error', 'Harga Flash Sale harus lebih rendah dari harga normal produk (Rp ' . number_format($product->price, 0, ',', '.') . ').');
        }

        $discountPct = (int) round((($product->price - $request->flash_sale_price) / $product->price) * 100);

        FlashSaleItem::updateOrCreate(
            [
                'flash_sale_id' => $flashSale->id,
                'product_id'    => $product->id,
            ],
            [
                'flash_sale_price'    => $request->flash_sale_price,
                'discount_percentage' => max(1, $discountPct),
                'stock_allocated'     => $request->stock_allocated,
                'is_active'           => true,
            ]
        );

        return back()->with('success', "Produk '{$product->name}' berhasil ditambahkan ke Flash Sale.");
    }

    public function updateItem(Request $request, FlashSale $flashSale, FlashSaleItem $item): RedirectResponse
    {
        $request->validate([
            'flash_sale_price' => ['required', 'numeric', 'min:1'],
            'stock_allocated'  => ['required', 'integer', 'min:1'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $product = $item->product;

        if ($request->flash_sale_price >= $product->price) {
            return back()->with('error', 'Harga Flash Sale harus lebih rendah dari harga normal produk.');
        }

        $discountPct = (int) round((($product->price - $request->flash_sale_price) / $product->price) * 100);

        $item->update([
            'flash_sale_price'    => $request->flash_sale_price,
            'discount_percentage' => max(1, $discountPct),
            'stock_allocated'     => $request->stock_allocated,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Produk '{$product->name}' pada Flash Sale berhasil diperbarui.");
    }

    public function removeItem(FlashSale $flashSale, FlashSaleItem $item): RedirectResponse
    {
        $productName = $item->product->name ?? 'Produk';
        $item->delete();

        return back()->with('success', "Produk '{$productName}' berhasil dihapus dari Flash Sale.");
    }
}
