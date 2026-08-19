<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): View|RedirectResponse
    {
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

        $grandTotal = max(0, $subtotal - $voucherDiscount);

        return view('customer.order.checkout', compact(
            'carts',
            'subtotal',
            'itemsTotal',
            'productSavings',
            'voucherDiscount',
            'grandTotal',
            'addresses',
            'defaultAddress',
            'appliedVoucher'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'address_id' => ['required', 'exists:user_addresses,id'],
        ], [
            'address_id.required' => 'Pilih alamat pengiriman terlebih dahulu.',
        ]);

        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $shippingAddress = "{$address->recipient_name} ({$address->phone})\n{$address->full_address}, {$address->city}, {$address->province} {$address->postal_code}";

        $carts = Cart::with(['product.store', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        foreach ($carts as $cart) {
            if ($cart->product->stock < $cart->quantity) {
                return redirect()->route('customer.cart.index')->with('error', "Stok untuk produk '{$cart->product->name}' tidak mencukupi. Sisa stok: {$cart->product->stock}");
            }
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

        $firstOrderId = null;

        DB::transaction(function () use ($groupedByStore, $shippingAddress, $totalSubtotal, $appliedVoucher, $totalVoucherDiscount, $storeCount, &$firstOrderId) {
            $remainingVoucherDiscount = $totalVoucherDiscount;
            $processedStores = 0;

            foreach ($groupedByStore as $storeId => $items) {
                $processedStores++;
                $storeSubtotal = $items->sum(fn ($i) => $i->product->final_price * $i->quantity);

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
                            $storeDiscount = round(($storeSubtotal / $totalSubtotal) * $totalVoucherDiscount);
                            $remainingVoucherDiscount -= $storeDiscount;
                        }
                    }
                }

                $storeTotalAmount = max(0, $storeSubtotal - $storeDiscount);

                $order = Order::create([
                    'invoice_number'   => 'INV-' . strtoupper(Str::random(10)),
                    'user_id'          => Auth::id(),
                    'store_id'         => $storeId,
                    'total_amount'     => $storeTotalAmount,
                    'voucher_code'     => $orderVoucherCode,
                    'discount_amount'  => $storeDiscount,
                    'status'           => 'pending',
                    'shipping_address' => $shippingAddress,
                ]);

                if (!$firstOrderId) {
                    $firstOrderId = $order->id;
                }

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->final_price,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                }

                // Notify seller
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
                $appliedVoucher->increment('used_count');
                session()->forget('applied_voucher');
            }

            Cart::where('user_id', Auth::id())->delete();
        });

        $firstOrder = Order::find($firstOrderId);
        return redirect()->route('customer.order.payment', $firstOrder)
            ->with('success', 'Pesanan Anda berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function payment(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.order.payment', compact('order'));
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
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

        return redirect()->route('customer.cart.index')->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan segera memproses pesanan Anda.');
    }

    public function confirmReceived(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($order->status !== 'shipped') {
            return redirect()->route('customer.dashboard')->with('error', 'Hanya pesanan yang sedang dalam pengiriman yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            // Auto-credit 95% balance to seller store (5% platform commission kept)
            $sellerEarnings = round($order->total_amount * 0.95);
            $order->store->increment('balance', $sellerEarnings);

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
        });

        return redirect()->route('customer.dashboard')->with('success', 'Pesanan berhasil diselesaikan! Silakan berikan ulasan untuk produk yang telah Anda terima.');
    }
}
