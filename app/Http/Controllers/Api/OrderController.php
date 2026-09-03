<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('uuid', $id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

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

        try {
            $createdOrder = DB::transaction(function () use ($user, $carts, $request) {
                $totalAmount = 0;
                $lockedProducts = [];
                $firstStoreId = null;

                // 1. Pessimistic concurrency locking & stock check on each product
                foreach ($carts as $cart) {
                    $product = Product::where('id', $cart->product_id)->lockForUpdate()->first();

                    if (!$product || !$product->is_active) {
                        throw new \DomainException("Produk '{$cart->product?->name}' saat ini tidak aktif atau tidak tersedia.");
                    }

                    if ($product->stock < $cart->quantity) {
                        throw new \DomainException("Stok untuk produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock} unit).");
                    }

                    if (!$firstStoreId) {
                        $firstStoreId = $product->store_id;
                    }

                    $price = (float) $product->final_price;
                    $totalAmount += ($price * $cart->quantity);

                    $lockedProducts[] = [
                        'cart'     => $cart,
                        'product'  => $product,
                        'price'    => $price,
                        'quantity' => $cart->quantity,
                        'variant'  => $cart->variant,
                    ];
                }

                // 2. Validate Voucher
                $discountAmount = 0;
                $appliedVoucher = null;
                if ($request->filled('voucher_code')) {
                    $appliedVoucher = \App\Models\Voucher::active()->where('code', $request->voucher_code)->first();
                    if (!$appliedVoucher) {
                        throw new \DomainException('Voucher tidak valid atau sudah kedaluwarsa.');
                    }

                    if ($appliedVoucher->is_store_voucher) {
                        $applicableSubtotal = collect($lockedProducts)
                            ->filter(fn($i) => $i['product']->store_id == $appliedVoucher->store_id)
                            ->sum(fn($i) => $i['price'] * $i['quantity']);
                    } else {
                        $applicableSubtotal = $totalAmount;
                    }

                    $validation = $appliedVoucher->validateForSubtotal($applicableSubtotal);
                    if (!$validation['valid']) {
                        throw new \DomainException($validation['message']);
                    }

                    $discountAmount = $appliedVoucher->calculateDiscount($applicableSubtotal);
                }

                $finalAmount = max(0, $totalAmount - $discountAmount);
                $invoiceNumber = 'INV-' . strtoupper(Str::random(10));

                // 3. Create Order
                $order = Order::create([
                    'invoice_number'   => $invoiceNumber,
                    'user_id'          => $user->id,
                    'store_id'         => $firstStoreId ?: 1,
                    'total_amount'     => $finalAmount,
                    'voucher_code'     => $appliedVoucher ? $appliedVoucher->code : null,
                    'discount_amount'  => $discountAmount,
                    'status'           => Order::STATUS_PENDING,
                    'payment_method'   => $request->payment_method ?? 'QRIS Instant',
                    'shipping_address' => $request->shipping_address,
                    'shipping_courier' => $request->get('courier', 'J&T Express'),
                    'shipping_service' => 'REG',
                    'shipping_cost'    => 0,
                    'total_weight'     => 1.0,
                    'expires_at'       => now()->addHours(24),
                ]);

                // 4. Create Order Items and atomic stock decrement
                foreach ($lockedProducts as $itemData) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $itemData['product']->id,
                        'quantity'   => $itemData['quantity'],
                        'price'      => $itemData['price'],
                        'variant'    => $itemData['variant'],
                    ]);

                    Product::where('id', $itemData['product']->id)->decrement('stock', $itemData['quantity']);
                    Product::where('id', $itemData['product']->id)->increment('sold_count', $itemData['quantity']);
                }

                // 5. Decrement voucher quota
                if ($appliedVoucher) {
                    $appliedVoucher->decrement('quota');
                }

                // 6. Delete checked-out cart items
                $carts->each->delete();

                return $order;
            });

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan berhasil dibuat!',
                'order_id'       => $createdOrder->id,
                'order_number'   => $createdOrder->invoice_number,
                'invoice_number' => $createdOrder->invoice_number,
                'total_amount'   => (float) $createdOrder->total_amount,
            ], 201);

        } catch (\DomainException $de) {
            return response()->json([
                'success' => false,
                'message' => $de->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('API Checkout Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pesanan. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Simulate payment for pending order.
     */
    public function pay(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('uuid', $id);
        })->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status === Order::STATUS_PENDING) {
            $order->transitionTo(Order::STATUS_PROCESSING);
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diverifikasi!',
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
     * Cancel an active pending or processing order with automated refund & stock recovery.
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('uuid', $id);
        })->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAID, Order::STATUS_PROCESSING], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan menunggu pembayaran atau sedang diproses yang dapat dibatalkan.',
            ], 422);
        }

        $reason = Str::limit(trim($request->input('reason', 'Dibatalkan oleh pembeli via aplikasi.')), 500);

        try {
            WalletService::refundAndCancelOrder($order, $reason);

            return response()->json([
                'success' => true,
                'message' => "Pesanan #{$order->invoice_number} berhasil dibatalkan. Stok barang telah dipulihkan.",
                'status'  => Order::STATUS_CANCELLED,
            ]);
        } catch (\DomainException $de) {
            return response()->json([
                'success' => false,
                'message' => $de->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('API Order Cancel Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembatalan pesanan.',
            ], 500);
        }
    }

    /**
     * Customer confirms delivery / completes order.
     */
    public function confirmReceived(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('uuid', $id);
        })->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($order->status === Order::STATUS_COMPLETED) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan sudah selesai.',
                'status'  => Order::STATUS_COMPLETED,
            ]);
        }

        if (!in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan yang sedang dalam pengiriman yang dapat diselesaikan.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($order) {
                $order->transitionTo(Order::STATUS_COMPLETED, [
                    'shipping_status' => 'delivered',
                ]);

                // Auto-credit 85% balance to seller store if not credited yet
                if (!$order->seller_credited_at && $order->store) {
                    $sellerEarnings = round($order->total_amount * 0.85);
                    WalletService::creditStore($order->store, (float) $sellerEarnings, "Penyelesaian pesanan #{$order->invoice_number}");
                    $order->update(['seller_credited_at' => now()]);
                }
            });

            \App\Models\AppNotification::create([
                'user_id' => $order->user_id,
                'title'   => 'Pesanan Selesai 🎉',
                'message' => "Terima kasih! Pesanan #{$order->invoice_number} telah Anda konfirmasi selesai. Berikan ulasan untuk membantu penjual.",
                'link'    => route('customer.dashboard'),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Pesanan #{$order->invoice_number} telah berhasil diselesaikan!",
                'status'  => Order::STATUS_COMPLETED,
            ]);
        } catch (\Throwable $e) {
            Log::error('API Confirm Received Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonfirmasi penyelesaian pesanan.',
            ], 500);
        }
    }

    /**
     * Get real-time delivery tracking timeline for an order.
     */
    public function tracking(Request $request, $id): JsonResponse
    {
        $order = $request->user()->orders()->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('uuid', $id);
        })->first();

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
                'description' => in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]) 
                                 ? 'Pembayaran berhasil diverifikasi. Penjual sedang menyiapkan barang.' 
                                 : 'Menunggu proses pembayaran oleh pembeli.',
                'time'        => in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]) ? $createdAt->addMinutes(5)->format('d M Y, H:i') : '-',
                'is_completed'=> in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]),
            ],
            [
                'title'       => 'Paket Diserahkan ke Kurir',
                'description' => in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]) 
                                 ? "Paket telah di-pickup oleh kurir {$courier} (No. Resi: {$trackingNo})." 
                                 : 'Menunggu paket diserahkan ke jasa kirim.',
                'time'        => in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]) ? $createdAt->addHours(4)->format('d M Y, H:i') : '-',
                'is_completed'=> in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_COMPLETED]),
            ],
            [
                'title'       => 'Paket Tiba di Tujuan',
                'description' => $order->status === Order::STATUS_COMPLETED 
                                 ? 'Paket telah diterima dengan baik oleh penerima.' 
                                 : 'Paket dalam perjalanan menuju alamat tujuan.',
                'time'        => $order->status === Order::STATUS_COMPLETED ? ($order->completed_at ? $order->completed_at->format('d M Y, H:i') : 'Selesai') : '-',
                'is_completed'=> $order->status === Order::STATUS_COMPLETED,
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

        $order = $request->user()->orders()->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('uuid', $id);
        })->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($order->status !== Order::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan hanya dapat diberikan untuk pesanan yang telah selesai.',
            ], 422);
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
        $product = Product::find($productId);
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
