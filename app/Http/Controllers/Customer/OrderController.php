<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): View|RedirectResponse
    {
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('warning', 'Email akun Anda belum diverifikasi. Silakan masukkan kode OTP yang dikirimkan ke email untuk mulai berbelanja.');
        }

        $carts = Cart::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $subtotal = $carts->sum(function ($item) {
            return $item->product->final_price * $item->quantity;
        });

        $itemsTotal = $carts->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $productSavings = $itemsTotal - $subtotal;

        $voucherDiscount = 0;
        $appliedVoucher = null;

        if (session()->has('applied_voucher')) {
            $appliedVoucher = Voucher::active()->with('store')->where('code', session('applied_voucher'))->first();
            if ($appliedVoucher) {
                if ($appliedVoucher->is_store_voucher) {
                    $storeItems = $carts->filter(fn ($item) => $item->product && $item->product->store_id == $appliedVoucher->store_id);
                    $applicableSubtotal = $storeItems->sum(fn ($item) => $item->product->final_price * $item->quantity);
                } else {
                    $applicableSubtotal = $subtotal;
                }

                $validation = $appliedVoucher->validateForSubtotal($applicableSubtotal);
                if ($validation['valid'] && $applicableSubtotal > 0) {
                    $voucherDiscount = $appliedVoucher->calculateDiscount($applicableSubtotal);
                } else {
                    session()->forget('applied_voucher');
                    $appliedVoucher = null;
                }
            }
        }

        $addresses = UserAddress::where('user_id', Auth::id())->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        // Hitung opsi pengiriman kurir per toko
        $groupedByStore = $carts->groupBy(fn ($item) => $item->product->store_id);
        $storeShippingData = [];
        $totalInitialShipping = 0;

        foreach ($groupedByStore as $storeId => $items) {
            $firstStore = $items->first()->product?->store ?? null;
            $storeWeight = $items->sum(fn ($it) => max(0.2, (float) ($it->product?->weight ?? 0.5)) * $it->quantity);
            $options = ShippingService::getAvailableOptions($storeWeight, $defaultAddress?->city, null, $firstStore);
            $defaultOption = $options[0] ?? ShippingService::getDefaultOption($storeWeight, $defaultAddress?->city, null, $firstStore);

            // Jika tidak ada store, skip обработка
            if (!$firstStore) {
                continue;
            }

            $storeShippingData[$storeId] = [
                'store_name'     => $firstStore?->name ?? 'Official Store',
                'origin_city'    => $firstStore?->effective_city ?? 'Jakarta Pusat',
                'weight'         => $storeWeight,
                'options'        => $options,
                'selected_id'    => $defaultOption['id'] ?? 'NDX_REG',
                'selected_cost'  => $defaultOption['cost'],
                'is_same_city'   => $defaultOption['is_same_city'] ?? false,
            ];

            $totalInitialShipping += $defaultOption['cost'];
        }

        $grandTotal = max(0, $subtotal - $voucherDiscount + $totalInitialShipping);
        $paymentChannels = PaymentService::PAYMENT_CHANNELS;

        return view('customer.order.checkout', compact(
            'carts',
            'subtotal',
            'itemsTotal',
            'productSavings',
            'voucherDiscount',
            'grandTotal',
            'addresses',
            'defaultAddress',
            'appliedVoucher',
            'storeShippingData',
            'totalInitialShipping',
            'paymentChannels'
        ));
    }

    public function calculateShippingOptions(Request $request): \Illuminate\Http\JsonResponse
    {
        $addressId = $request->input('address_id');
        $city = $request->input('city');

        if ($addressId) {
            $addr = UserAddress::where('id', $addressId)->where('user_id', Auth::id())->first();
            if ($addr && ! empty($addr->city)) {
                $city = $addr->city;
            }
        }

        $carts = Cart::with(['product.store'])
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Keranjang belanja kosong.'], 422);
        }

        $groupedByStore = $carts->groupBy(fn ($item) => $item->product->store_id);
        $storeShippingData = [];
        $totalShipping = 0;

        foreach ($groupedByStore as $storeId => $items) {
            $firstStore = $items->first()->product?->store ?? null;
            $storeWeight = $items->sum(fn ($it) => max(0.2, (float) ($it->product?->weight ?? 0.5)) * $it->quantity);
            $options = ShippingService::getAvailableOptions($storeWeight, $city, null, $firstStore);
            $defaultOption = $options[0] ?? ShippingService::getDefaultOption($storeWeight, $city, null, $firstStore);

            // Skip jika produk tidak punya toko
            if (!$firstStore) {
                continue;
            }

            $storeShippingData[$storeId] = [
                'store_name'     => $firstStore?->name ?? 'Official Store',
                'origin_city'    => $firstStore?->effective_city ?? 'Jakarta Pusat',
                'weight'         => $storeWeight,
                'options'        => $options,
                'selected_id'    => $defaultOption['id'] ?? 'NDX_REG',
                'selected_cost'  => $defaultOption['cost'],
                'is_same_city'   => $defaultOption['is_same_city'] ?? false,
            ];

            $totalShipping += $defaultOption['cost'];
        }

        return response()->json([
            'status'            => 'success',
            'city'              => $city,
            'storeShippingData' => $storeShippingData,
            'totalShipping'     => $totalShipping,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $hasAddressId = $request->filled('address_id');

        $request->validate([
            'address_id'       => [$hasAddressId ? 'required' : 'nullable', 'exists:user_addresses,id'],
            'shipping_address' => [$hasAddressId ? 'nullable' : 'required', 'string', 'min:5', 'max:1000'],
            'payment_method'   => ['nullable', 'string'],
        ], [
            'address_id.required'       => 'Pilih alamat pengiriman terlebih dahulu.',
            'shipping_address.required' => 'Masukkan alamat pengiriman tujuan terlebih dahulu.',
            'shipping_address.min'      => 'Alamat pengiriman minimal 5 karakter.',
        ]);

        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('warning', 'Email akun Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu sebelum melakukan transaksi pesanan.');
        }

        if ($hasAddressId) {
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->first();

            if (! $address) {
                return back()->withErrors(['address_id' => 'Alamat pengiriman yang dipilih tidak valid.'])->withInput();
            }

            $addressParts = array_filter([$address->district, $address->city, $address->province, $address->postal_code]);
            $location = ! empty($addressParts) ? ', ' . implode(', ', $addressParts) : '';
            $notesPart = ! empty($address->notes) ? "\n(Patokan: {$address->notes})" : '';
            $shippingAddress = "{$address->recipient_name} ({$address->phone})\n{$address->full_address}{$location}{$notesPart}";
            $destinationCity = $address->city;
        } else {
            $shippingAddress = trim($request->shipping_address);
            $destinationCity = null;

            // If user's profile address is empty, update it
            if (empty($user->address)) {
                $user->update(['address' => $shippingAddress]);
            }

            // If user has no saved addresses, automatically save this as default address
            if (UserAddress::where('user_id', $user->id)->count() === 0) {
                UserAddress::create([
                    'user_id'        => $user->id,
                    'label'          => 'Alamat Utama',
                    'recipient_name' => $user->name,
                    'phone'          => $user->phone ?? '-',
                    'full_address'   => $shippingAddress,
                    'is_default'     => true,
                ]);
            }
        }

        $carts = Cart::with(['product.store', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $totalSubtotal = $carts->sum(fn ($item) => $item->product->final_price * $item->quantity);

        $appliedVoucher = null;
        $totalVoucherDiscount = 0;

        if (session()->has('applied_voucher')) {
            $appliedVoucher = Voucher::active()->where('code', session('applied_voucher'))->first();
            if ($appliedVoucher) {
                if ($appliedVoucher->is_store_voucher) {
                    $applicableSubtotal = $carts->filter(fn ($item) => $item->product && $item->product->store_id == $appliedVoucher->store_id)
                        ->sum(fn ($item) => $item->product->final_price * $item->quantity);
                } else {
                    $applicableSubtotal = $totalSubtotal;
                }

                $validation = $appliedVoucher->validateForSubtotal($applicableSubtotal);
                if ($validation['valid'] && $applicableSubtotal > 0) {
                    $totalVoucherDiscount = $appliedVoucher->calculateDiscount($applicableSubtotal);
                }
            }
        }

        $groupedByStore = $carts->groupBy(fn ($item) => $item->product->store_id);
        $storeCount = $groupedByStore->count();
        $courierInputs = $request->input('couriers', []);
        $paymentMethod = $request->input('payment_method', 'qris');

        // Proteksi Duplikasi Checkout (Idempotency Guard)
        $recentOrder = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subSeconds(30))
            ->latest()
            ->first();

        if ($recentOrder) {
            return redirect()->route('customer.order.payment', $recentOrder)
                ->with('info', 'Pesanan Anda sudah dibuat sebelumnya. Silakan selesaikan pembayaran.');
        }

        $firstOrderId = null;

        try {
            DB::transaction(function () use ($user, $groupedByStore, $shippingAddress, $destinationCity, $totalSubtotal, $appliedVoucher, $totalVoucherDiscount, $storeCount, $courierInputs, $paymentMethod, &$firstOrderId) {
                $remainingVoucherDiscount = $totalVoucherDiscount;
                $processedStores = 0;

                // Step 1: Pessimistic Concurrency Lock & Pre-validation of all items in cart
                $lockedStoreItems = [];
                foreach ($groupedByStore as $storeId => $items) {
                    $lockedStoreItems[$storeId] = [];
                    foreach ($items as $cartItem) {
                        $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                        if (! $product || ! $product->is_active) {
                            throw new \DomainException("Produk '{$cartItem->product?->name}' sudah tidak aktif atau tidak tersedia.");
                        }

                        if ($product->stock < $cartItem->quantity) {
                            throw new \DomainException("Stok produk '{$product->name}' tidak mencukupi (sisa stok: {$product->stock} unit).");
                        }

                        $lockedStoreItems[$storeId][] = [
                            'cartItem' => $cartItem,
                            'product'  => $product,
                            'price'    => (float) $product->final_price,
                            'quantity' => (int) $cartItem->quantity,
                            'variant'  => $cartItem->variant,
                        ];
                    }
                }

                // Step 2: Create Order per Store
                foreach ($lockedStoreItems as $storeId => $lockedItems) {
                    $processedStores++;
                    $storeSubtotal = collect($lockedItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
                    $storeWeight = collect($lockedItems)->sum(fn ($it) => max(0.2, (float) ($it['product']->weight ?? 0.5)) * $it['quantity']);

                    // Ambil pilihan kurir untuk toko ini
                    $courierKey = $courierInputs[$storeId] ?? 'NDX_REG';
                    if (str_starts_with($courierKey, 'NDX_')) {
                        $cCode = 'NDX';
                        $sCode = substr($courierKey, 4);
                    } else {
                        $parts = explode('_', $courierKey, 2);
                        $cCode = $parts[0] ?? 'NDX';
                        $sCode = $parts[1] ?? 'REG';
                    }

                    $firstStore = $lockedItems[0]['product']->store ?? null;
                    $shippingRate = ShippingService::calculateRate($cCode, $sCode, $storeWeight, $destinationCity, null, $firstStore);

                    $storeDiscount = 0;
                    $orderVoucherCode = null;

                    if ($appliedVoucher && $totalVoucherDiscount > 0) {
                        if ($appliedVoucher->is_store_voucher) {
                            if ($storeId == $appliedVoucher->store_id) {
                                $storeDiscount = $totalVoucherDiscount;
                                $orderVoucherCode = $appliedVoucher->code;
                            }
                        } else {
                            $orderVoucherCode = $appliedVoucher->code;
                            if ($processedStores === $storeCount) {
                                $storeDiscount = $remainingVoucherDiscount;
                            } else {
                                $storeDiscount = round(($storeSubtotal / max(1, $totalSubtotal)) * $totalVoucherDiscount);
                                $remainingVoucherDiscount -= $storeDiscount;
                            }
                        }
                    }

                    $storeTotalAmount = max(0, $storeSubtotal - $storeDiscount + $shippingRate['cost']);

                    $order = Order::create([
                        'invoice_number'   => 'INV-' . strtoupper(Str::random(10)),
                        'user_id'          => $user->id,
                        'store_id'         => $storeId,
                        'total_amount'     => $storeTotalAmount,
                        'voucher_code'     => $orderVoucherCode,
                        'discount_amount'  => $storeDiscount,
                        'shipping_courier' => $shippingRate['courier_code'],
                        'shipping_service' => $shippingRate['service_code'],
                        'shipping_cost'    => $shippingRate['cost'],
                        'total_weight'     => $storeWeight,
                        'payment_method'   => $paymentMethod,
                        'status'           => Order::STATUS_PENDING,
                        'expires_at'       => now()->addHours(24),
                        'shipping_address' => $shippingAddress,
                    ]);

                    if (! $firstOrderId) {
                        $firstOrderId = $order->id;
                    }

                    foreach ($lockedItems as $item) {
                        OrderItem::create([
                            'order_id'   => $order->id,
                            'product_id' => $item['product']->id,
                            'quantity'   => $item['quantity'],
                            'price'      => $item['price'],
                            'variant'    => $item['variant'],
                        ]);

                        $item['product']->decrement('stock', $item['quantity']);
                        $item['product']->increment('sold_count', $item['quantity']);
                    }

                    // Notifikasi ke seller
                    if ($order->store && $order->store->user_id) {
                        AppNotification::send(
                            $order->store->user_id,
                            'Pesanan Baru Masuk',
                            "Pesanan baru #{$order->invoice_number} senilai Rp " . number_format($order->total_amount, 0, ',', '.') . " menunggu konfirmasi pembayaran.",
                            'order',
                            route('seller.orders.index')
                        );
                    }
                }

                if ($appliedVoucher) {
                    $appliedVoucher->decrement('quota');
                    session()->forget('applied_voucher');
                }

                Cart::where('user_id', $user->id)->delete();
            });
        } catch (\DomainException $de) {
            return redirect()->route('customer.cart.index')->with('error', $de->getMessage());
        } catch (\Throwable $e) {
            Log::error('Web Store Order Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('customer.cart.index')->with('error', 'Terjadi kendala sistem saat memproses pesanan Anda. Silakan coba beberapa saat lagi.');
        }

        $firstOrder = Order::find($firstOrderId);
        return redirect()->route('customer.order.payment', $firstOrder)
            ->with('success', 'Pesanan Anda berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function payment(Request $request, Order $order): View|RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($order->status, ['processing', 'shipped', 'completed'])) {
            return redirect()->route('customer.dashboard')->with('info', 'Pesanan ini sudah berhasil dibayar.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('customer.dashboard')->with('error', 'Pesanan ini telah dibatalkan.');
        }

        if ($request->filled('method') && $request->method !== $order->payment_method) {
            $order->update(['payment_method' => $request->method]);
        }

        $order->loadMissing(['store', 'items.product', 'user']);

        $otherPendingOrders = Order::where('user_id', Auth::id())
            ->where('id', '!=', $order->id)
            ->where('status', Order::STATUS_PENDING)
            ->where('created_at', '>=', now()->subHours(24))
            ->with('store')
            ->get();

        $paymentChannels = PaymentService::PAYMENT_CHANNELS;
        $charge = PaymentService::createPaymentCharge($order, $order->payment_method ?: 'qris');

        return view('customer.order.payment', compact('order', 'paymentChannels', 'charge', 'otherPendingOrders'));
    }

    public function changePaymentMethod(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => ['required', 'string'],
        ]);

        $order->update([
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('customer.order.payment', $order)
            ->with('success', 'Metode pembayaran berhasil diubah!');
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'status'        => 'processing',
        ]);

        // Notify seller
        if ($order->store && $order->store->user_id) {
            AppNotification::send(
                $order->store->user_id,
                'Bukti Pembayaran Diunggah',
                "Bukti pembayaran untuk pesanan #{$order->invoice_number} telah diunggah oleh pembeli. Silakan proses pengiriman barang!",
                'order',
                route('seller.orders.index')
            );
        }

        return redirect()->route('customer.dashboard')->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan segera memproses pesanan Anda.');
    }

    public function confirmReceived(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($order->status === Order::STATUS_COMPLETED) {
            return redirect()->route('customer.dashboard')->with('info', 'Pesanan sudah berstatus selesai.');
        }

        if (! in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
            return redirect()->route('customer.dashboard')->with('error', 'Hanya pesanan yang sedang dalam pengiriman yang dapat diselesaikan.');
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

                    // Notify seller
                    if ($order->store && $order->store->user_id) {
                        AppNotification::send(
                            $order->store->user_id,
                            'Pesanan Selesai & Dana Masuk',
                            "Pesanan #{$order->invoice_number} telah diterima pembeli! Dana sebesar Rp " . number_format($sellerEarnings, 0, ',', '.') . " telah masuk ke saldo dompet toko Anda.",
                            'wallet',
                            route('seller.wallet.index')
                        );
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Customer confirmReceived error: ' . $e->getMessage());
            return redirect()->route('customer.dashboard')->with('error', 'Gagal mengonfirmasi pesanan selesai. Silakan coba lagi.');
        }

        return redirect()->route('customer.dashboard')->with('success', 'Pesanan berhasil diselesaikan! Silakan berikan ulasan untuk produk yang telah Anda terima.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
            }
            abort(403, 'Akses tidak sah.');
        }

        if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAID, Order::STATUS_PROCESSING], true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pesanan yang sudah dikirim atau selesai tidak dapat dibatalkan.'], 422);
            }
            return redirect()->route('customer.dashboard')->with('error', 'Pesanan yang sudah dikirim atau selesai tidak dapat dibatalkan.');
        }

        $reason = Str::limit(trim($request->input('cancel_reason', $request->input('reason', 'Dibatalkan oleh pembeli.'))), 500);

        try {
            WalletService::refundAndCancelOrder($order, $reason);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pesanan #{$order->invoice_number} berhasil dibatalkan. Stok produk telah dipulihkan.",
                    'status'  => Order::STATUS_CANCELLED,
                ]);
            }

            return redirect()->route('customer.dashboard')->with('success', "Pesanan #{$order->invoice_number} berhasil dibatalkan. Stok produk telah dipulihkan.");
        } catch (\DomainException $de) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $de->getMessage()], 422);
            }
            return redirect()->route('customer.dashboard')->with('error', $de->getMessage());
        } catch (\Throwable $e) {
            Log::error('Cancel order error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memproses pembatalan pesanan.'], 500);
            }
            return redirect()->route('customer.dashboard')->with('error', 'Gagal memproses pembatalan pesanan.');
        }
    }
}
