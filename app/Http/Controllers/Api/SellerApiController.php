<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerApiController extends Controller
{
    /**
     * 1. Pendaftaran Buka Toko Baru (Customer -> Seller)
     */
    public function registerStore(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
        ]);

        $user = Auth::user();

        // Create or get store
        $store = Store::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name'        => $request->name,
                'slug'        => Str::slug($request->name) . '-' . Str::random(5),
                'description' => $request->description ?? 'Toko Resmi di NitipDong',
                'address'     => $request->address,
                'city'        => $request->city,
                'phone'       => $request->phone,
                'is_active'   => true,
            ]
        );

        $user->role = 'seller';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Selamat! Toko Anda berhasil dibuka dan siap berjualan.',
            'store'   => $store,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * 2. Ringkasan Statistik Toko Seller
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki toko terdaftar.',
            ], 404);
        }

        $totalProducts = Product::where('store_id', $store->id)->count();
        $orders = Order::where('store_id', $store->id)->get();

        $totalSales = $orders->where('status', 'completed')->sum('total_amount');
        $pendingOrders = $orders->whereIn('status', ['pending', 'paid', 'processing'])->count();
        $completedOrders = $orders->where('status', 'completed')->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'store'            => $store,
                'total_products'   => $totalProducts,
                'total_sales'      => (float) $totalSales,
                'pending_orders'   => $pendingOrders,
                'completed_orders' => $completedOrders,
                'wallet_balance'   => (float) ($store->balance ?? $totalSales * 0.85),
            ],
        ]);
    }

    /**
     * 3. Daftar Produk Toko
     */
    public function products(): JsonResponse
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        if (!$store) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $products = Product::where('store_id', $store->id)->latest()->get()->map(function ($p) {
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'price'       => (float) $p->price,
                'stock'       => $p->stock,
                'image_url'   => $p->image_url,
                'category'    => $p->category?->name ?? 'Umum',
                'is_active'   => (bool) ($p->is_active ?? true),
                'sold_count'  => $p->sold_count ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    /**
     * 4. Tambah Produk Baru oleh Seller
     */
    public function storeProduct(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric|min:1000',
            'stock'       => 'required|integer|min:0|max:10000',
            'description' => 'required|string',
            'category_id' => 'nullable|integer',
            'image_url'   => 'nullable|string',
        ]);

        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan'], 404);
        }

        $product = Product::create([
            'store_id'    => $store->id,
            'category_id' => $request->category_id ?? 1,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . Str::random(5),
            'price'       => $request->price,
            'stock'       => $request->stock,
            'description' => $request->description,
            'image_url'   => $request->image_url ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80',
            'is_active'   => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke katalog toko Anda.',
            'data'    => $product,
        ]);
    }

    /**
     * 5. Hapus Produk Toko
     */
    public function deleteProduct($id): JsonResponse
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        $product = Product::where('id', $id)->where('store_id', $store->id)->first();
        if ($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
    }

    /**
     * 6. Daftar Pesanan Masuk Toko
     */
    public function orders(Request $request): JsonResponse
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        if (!$store) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $status = $request->query('status'); // 'all', 'processing', 'shipped', 'completed'

        $query = Order::with(['user', 'orderItems.product', 'courier'])
            ->where('store_id', $store->id);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get()->map(function ($order) {
            return [
                'id'               => $order->id,
                'invoice_number'   => $order->invoice_number,
                'status'           => $order->status,
                'total_amount'     => (float) $order->total_amount,
                'recipient_name'   => $order->user?->name ?? 'Pembeli',
                'recipient_phone'  => $order->user?->phone ?? '081234567890',
                'shipping_address' => $order->shipping_address ?? 'Alamat Pembeli',
                'courier_name'     => $order->courier?->name ?? 'Belum ada kurir',
                'items_count'      => $order->orderItems->count(),
                'created_at'       => $order->created_at?->format('d M Y, H:i'),
                'items'            => $order->orderItems->map(fn($item) => [
                    'name'     => $item->product?->name ?? 'Produk',
                    'quantity' => $item->quantity,
                    'price'    => (float) $item->price,
                    'image'    => $item->product?->image_url ?? '',
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * 7. Update Status Pesanan oleh Seller (Proses Pesanan / Siap Kirim)
     */
    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:processing,shipped,cancelled',
        ]);

        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        $order = Order::where('id', $id)->where('store_id', $store->id)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'status'  => $order->status,
        ]);
    }
}
