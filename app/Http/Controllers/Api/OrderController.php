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

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu sebelum melakukan transaksi pesanan.',
            ], 403);
        }

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

    /**
     * Cancel an active pending or processing order.
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where('id', $id)->orWhere('uuid', $id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan menunggu pembayaran atau sedang diproses yang dapat dibatalkan.',
            ], 422);
        }

        $reason = $request->input('reason', 'Dibatalkan oleh pembeli via aplikasi.');

        DB::transaction(function () use ($order, $reason) {
            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                }
            }

            // Restore voucher quota
            if ($order->voucher_code) {
                $voucher = \App\Models\Voucher::where('code', $order->voucher_code)->first();
                if ($voucher) {
                    $voucher->increment('quota');
                }
            }

            \App\Models\AppNotification::send(
                $order->user_id,
                'Pesanan Dibatalkan',
                "Pesanan #{$order->invoice_number} telah berhasil dibatalkan.",
                'order',
                route('customer.dashboard')
            );
        });

        // Cancel on Midtrans Sandbox
        if (!empty($order->payment_reference)) {
            try {
                $serverKey = config('services.midtrans.server_key', 'Mid-server-QRIG4umIOjT0Q4w1JDxzIc0c');
                \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                    ->timeout(5)
                    ->post("https://api.sandbox.midtrans.com/v2/{$order->payment_reference}/cancel");
            } catch (\Exception $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => "Pesanan #{$order->invoice_number} berhasil dibatalkan. Stok barang telah dipulihkan.",
            'status'  => 'cancelled',
        ]);
    }

    /**
     * Customer confirms delivery / completes order.
     */
    public function confirmReceived(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where('id', $id)->orWhere('uuid', $id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($order->status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan sudah selesai.',
                'status'  => 'completed',
            ]);
        }

        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        \App\Models\AppNotification::create([
            'user_id' => $order->user_id,
            'title'   => 'Pesanan Selesai 🎉',
            'message' => "Terima kasih! Pesanan #{$order->invoice_number} telah Anda konfirmasi selesai. Berikan ulasan untuk membantu penjual.",
            'link'    => route('customer.dashboard'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pesanan #{$order->invoice_number} telah berhasil diselesaikan!",
            'status'  => 'completed',
        ]);
    }

    /**
     * Get real-time delivery tracking timeline for an order.
     */
    public function tracking(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where('id', $id)->orWhere('uuid', $id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $courier = $order->shipping_courier ?? 'J&T Express';
        $trackingNo = $order->tracking_number ?: ('ND' . strtoupper(substr(md5($order->id . $order->created_at), 0, 10)));
        $createdAt = $order->created_at;

        $timeline = [
            [
                'title'       => 'Pesanan Berhasil Dibuat',
                'description' => "Invoice #{$order->invoice_number} telah dibuat dalam sistem NitipDong.",
                'time'        => $createdAt->format('d M Y, H:i'),
                'is_completed'=> true,
            ],
            [
                'title'       => 'Pembayaran Terkonfirmasi',
                'description' => in_array($order->status, ['processing', 'shipped', 'completed']) 
                                 ? 'Pembayaran berhasil diverifikasi. Penjual sedang menyiapkan barang.' 
                                 : 'Menunggu proses pembayaran oleh pembeli.',
                'time'        => in_array($order->status, ['processing', 'shipped', 'completed']) ? $createdAt->addMinutes(5)->format('d M Y, H:i') : '-',
                'is_completed'=> in_array($order->status, ['processing', 'shipped', 'completed']),
            ],
            [
                'title'       => 'Paket Diserahkan ke Kurir',
                'description' => in_array($order->status, ['shipped', 'completed']) 
                                 ? "Paket telah di-pickup oleh kurir {$courier} (No. Resi: {$trackingNo})." 
                                 : 'Menunggu paket diserahkan ke jasa kirim.',
                'time'        => in_array($order->status, ['shipped', 'completed']) ? $createdAt->addHours(4)->format('d M Y, H:i') : '-',
                'is_completed'=> in_array($order->status, ['shipped', 'completed']),
            ],
            [
                'title'       => 'Paket Tiba di Tujuan',
                'description' => $order->status === 'completed' 
                                 ? 'Paket telah diterima dengan baik oleh penerima.' 
                                 : 'Paket dalam perjalanan menuju alamat tujuan.',
                'time'        => $order->status === 'completed' ? ($order->completed_at ? $order->completed_at->format('d M Y, H:i') : 'Selesai') : '-',
                'is_completed'=> $order->status === 'completed',
            ],
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'order_id'       => $order->id,
                'invoice_number' => $order->invoice_number,
                'status'         => $order->status,
                'courier'        => $courier,
                'tracking_number'=> $trackingNo,
                'recipient'      => $request->user()->name,
                'address'        => $order->shipping_address ?? $request->user()->address,
                'timeline'       => $timeline,
            ],
        ]);
    }

    /**
     * Store product review for completed order.
     */
    public function storeReview(Request $request, $id): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'comment'    => ['nullable', 'string', 'max:1000'],
        ]);

        $order = $request->user()->orders()->where('id', $id)->orWhere('uuid', $id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();
        $productId = (int) $request->product_id;
        $orderItem = $order->orderItems()->where('product_id', $productId)->first();

        $review = \App\Models\Review::updateOrCreate(
            [
                'user_id'    => $user->id,
                'product_id' => $productId,
                'order_id'   => $order->id,
            ],
            [
                'order_item_id' => $orderItem?->id,
                'rating'        => (int) $request->rating,
                'comment'       => $request->comment ?: 'Produk sangat bagus sesuai deskripsi dan pengiriman cepat!',
                'is_anonymous'  => false,
            ]
        );

        // Recalculate product rating
        $product = \App\Models\Product::find($productId);
        if ($product) {
            $product->recalculateRating();
        }

        return response()->json([
            'success' => true,
            'message' => 'Ulasan Anda berhasil dikirim! Terima kasih atas feedback Anda.',
            'data'    => $review,
        ]);
    }
}
