<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get list of orders for authenticated customer.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->orders()->with(['items.product.store'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        $formatted = $orders->map(function ($order) {
            return [
                'id'             => $order->id,
                'order_number'   => $order->order_number ?? ('ORD-' . $order->id),
                'total_amount'   => (float) $order->total_amount,
                'status'         => $order->status,
                'status_label'   => ucfirst($order->status),
                'payment_status' => $order->payment_status ?? 'pending',
                'created_at'     => $order->created_at->format('d M Y, H:i'),
                'items_count'    => $order->items->count(),
                'first_product'  => $order->items->first()?->product ? [
                    'name'      => $order->items->first()->product->name,
                    'image_url' => $order->items->first()->product->image_url ?? asset('img/saksershop-logo.png'),
                    'quantity'  => $order->items->first()->quantity,
                    'price'     => (float) $order->items->first()->price,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * Get specific order details by ID.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()
            ->with(['items.product.store', 'store'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $order->id,
                'order_number'    => $order->order_number ?? ('ORD-' . $order->id),
                'status'          => $order->status,
                'payment_status'  => $order->payment_status ?? 'pending',
                'payment_method'  => $order->payment_method ?? 'QRIS Instant',
                'shipping_address'=> $order->shipping_address ?? $request->user()->address,
                'courier'         => $order->courier ?? 'JNE Regular',
                'tracking_number' => $order->tracking_number,
                'subtotal'        => (float) ($order->total_amount - ($order->shipping_fee ?? 0)),
                'shipping_fee'    => (float) ($order->shipping_fee ?? 0),
                'total_amount'    => (float) $order->total_amount,
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'items'           => $order->items->map(function ($item) {
                    return [
                        'id'         => $item->id,
                        'name'       => $item->product?->name ?? 'Produk',
                        'image_url'  => $item->product?->image_url ?? asset('img/saksershop-logo.png'),
                        'price'      => (float) $item->price,
                        'quantity'   => (int) $item->quantity,
                        'variant'    => $item->variant,
                        'subtotal'   => (float) ($item->price * $item->quantity),
                        'store_name' => $item->product?->store->name ?? 'Official Store',
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create new order from Cart or Direct Checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address' => ['required', 'string'],
            'payment_method'   => ['required', 'string'],
            'cart_ids'         => ['nullable', 'array'],
        ]);

        $user = $request->user();

        $cartQuery = $user->carts()->with('product.store');
        if (!empty($request->cart_ids)) {
            $cartQuery->whereIn('id', $request->cart_ids);
        }
        $carts = $cartQuery->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $firstStoreId = $carts->first()->product?->store_id ?? 1;

            // Calculate total
            foreach ($carts as $cart) {
                $totalAmount += ($cart->product->final_price * $cart->quantity);
            }

            // Create Order
            $order = Order::create([
                'user_id'          => $user->id,
                'store_id'         => $firstStoreId,
                'order_number'     => 'NTD-' . strtoupper(Str::random(8)),
                'total_amount'     => $totalAmount,
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'payment_method'   => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'courier'          => $request->get('courier', 'J&T Express'),
                'shipping_fee'     => 0,
            ]);

            // Create Order Items & Decrement Stock
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity'   => $cart->quantity,
                    'price'      => $cart->product->final_price,
                    'variant'    => $cart->variant,
                ]);

                // Reduce stock
                $cart->product->decrement('stock', $cart->quantity);
                $cart->product->increment('sold_count', $cart->quantity);
            }

            // Clear checked-out cart items
            $carts->each->delete();

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat!',
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => (float) $totalAmount,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
