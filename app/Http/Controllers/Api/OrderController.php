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
        $query = $request->user()->orders()->with(['orderItems.product.store'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        $formatted = $orders->map(function ($order) {
            $firstItem = $order->orderItems->first();
            return [
                'id'             => $order->id,
                'order_number'   => $order->invoice_number,
                'invoice_number' => $order->invoice_number,
                'total_amount'   => (float) $order->total_amount,
                'status'         => $order->status,
                'status_label'   => ucfirst($order->status),
                'payment_status' => in_array($order->status, ['processing', 'shipped', 'completed']) ? 'paid' : 'pending',
                'created_at'     => $order->created_at->format('d M Y, H:i'),
                'items_count'    => $order->orderItems->count(),
                'first_product'  => $firstItem?->product ? [
                    'name'      => $firstItem->product->name,
                    'image_url' => $firstItem->product->image_url ?? asset('img/saksershop-logo.png'),
                    'quantity'  => $firstItem->quantity,
                    'price'     => (float) $firstItem->price,
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
            ->with(['orderItems.product.store', 'store'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $order->id,
                'order_number'    => $order->invoice_number,
                'invoice_number'  => $order->invoice_number,
                'status'          => $order->status,
                'payment_status'  => in_array($order->status, ['processing', 'shipped', 'completed']) ? 'paid' : 'pending',
                'payment_method'  => $order->payment_method ?? 'QRIS Instant',
                'shipping_address'=> $order->shipping_address ?? $request->user()->address,
                'courier'         => $order->shipping_courier ?? 'JNE Regular',
                'tracking_number' => $order->tracking_number,
                'subtotal'        => (float) ($order->total_amount - ($order->shipping_cost ?? 0)),
                'shipping_fee'    => (float) ($order->shipping_cost ?? 0),
                'total_amount'    => (float) $order->total_amount,
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'items'           => $order->orderItems->map(function ($item) {
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
            'voucher_code'     => ['nullable', 'string'],
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

            // Apply Voucher
            $discountAmount = 0;
            $appliedVoucher = null;
            if ($request->filled('voucher_code')) {
                $appliedVoucher = \App\Models\Voucher::active()->where('code', $request->voucher_code)->first();
                if (!$appliedVoucher) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher tidak valid atau sudah kadaluarsa.',
                    ], 422);
                }

                if ($appliedVoucher->is_store_voucher) {
                    $applicableCarts = $carts->filter(fn($c) => $c->product->store_id == $appliedVoucher->store_id);
                } else {
                    $applicableCarts = $carts;
                }

                $applicableSubtotal = 0;
                foreach ($applicableCarts as $c) {
                    $applicableSubtotal += ($c->product->final_price * $c->quantity);
                }

                $validation = $appliedVoucher->validateForSubtotal($applicableSubtotal);
                if (!$validation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['message'],
                    ], 422);
                }

                $discountAmount = $appliedVoucher->calculateDiscount($applicableSubtotal);
            }

            $finalAmount = max(0, $totalAmount - $discountAmount);
            $invoiceNumber = 'INV-' . strtoupper(Str::random(10));

            // Create Order
            $order = Order::create([
                'invoice_number'   => $invoiceNumber,
                'user_id'          => $user->id,
                'store_id'         => $firstStoreId,
                'total_amount'     => $finalAmount,
                'voucher_code'     => $appliedVoucher ? $appliedVoucher->code : null,
                'discount_amount'  => $discountAmount,
                'status'           => 'pending',
                'payment_method'   => $request->payment_method ?? 'QRIS Instant',
                'shipping_address' => $request->shipping_address,
                'shipping_courier' => $request->get('courier', 'J&T Express'),
                'shipping_service' => 'REG',
                'shipping_cost'    => 0,
                'total_weight'     => 1.0,
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

            if ($appliedVoucher) {
                $appliedVoucher->decrement('quota');
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan berhasil dibuat!',
                'order_id'       => $order->id,
                'order_number'   => $invoiceNumber,
                'invoice_number' => $invoiceNumber,
                'total_amount'   => (float) $finalAmount,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simulate payment for pending order.
     */
    public function pay(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($id);
        if ($order->status === 'pending') {
            $order->update([
                'status' => 'processing',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimulasikan!',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Pesanan tidak dalam status menunggu pembayaran.',
        ], 422);
    }

    /**
     * Validate a promo voucher coupon before checkout.
     */
    public function validateVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'voucher_code' => ['required', 'string'],
        ]);

        $voucher = \App\Models\Voucher::active()->where('code', $request->voucher_code)->first();
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        // Calculate subtotal from cart
        $carts = $request->user()->carts()->with('product.store')->get();
        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja Anda kosong.',
            ], 422);
        }

        if ($voucher->is_store_voucher) {
            $applicableCarts = $carts->filter(fn($c) => $c->product->store_id == $voucher->store_id);
        } else {
            $applicableCarts = $carts;
        }

        $applicableSubtotal = 0;
        foreach ($applicableCarts as $c) {
            $applicableSubtotal += ($c->product->final_price * $c->quantity);
        }

        $validation = $voucher->validateForSubtotal($applicableSubtotal);
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 422);
        }

        $discount = $voucher->calculateDiscount($applicableSubtotal);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'data'    => [
                'code'            => $voucher->code,
                'discount_amount' => (float) $discount,
                'min_spend'       => (float) $voucher->min_spend,
            ],
        ]);
    }
}
