<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
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

        $finalTotal = max(0, $subtotal - $voucherDiscount);

        return view('customer.order.checkout', compact(
            'carts',
            'itemsTotal',
            'subtotal',
            'productSavings',
            'voucherDiscount',
            'appliedVoucher',
            'finalTotal'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'min:15'],
        ]);

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $totalSubtotal = $carts->sum(fn ($i) => $i->product->final_price * $i->quantity);

        $appliedVoucher = null;
        $totalVoucherDiscount = 0;

        if (session()->has('applied_voucher')) {
            $appliedVoucher = Voucher::active()->where('code', session('applied_voucher'))->first();
            if ($appliedVoucher) {
                if ($appliedVoucher->is_store_voucher) {
                    $storeItems = $carts->filter(fn ($i) => $i->product && $i->product->store_id == $appliedVoucher->store_id);
                    $applicableSubtotal = $storeItems->sum(fn ($i) => $i->product->final_price * $i->quantity);
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

        DB::transaction(function () use ($groupedByStore, $request, $totalSubtotal, $appliedVoucher, $totalVoucherDiscount, $storeCount) {
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
                    'shipping_address' => $request->shipping_address,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->final_price,
                    ]);

                    $item->product->decrement('stock', $item->quantity);

                    if ($fsi = $item->product->current_flash_sale_item) {
                        $fsi->increment('stock_sold', $item->quantity);
                    }
                }
            }

            if ($appliedVoucher && $totalVoucherDiscount > 0) {
                $appliedVoucher->decrement('quota');
            }

            Cart::where('user_id', Auth::id())->delete();
            session()->forget('applied_voucher');
        });

        return redirect()->route('customer.dashboard')->with('success', 'Pesanan berhasil dibuat! Seller akan segera memproses barang Anda.');
    }

    public function payment(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending' || $order->payment_proof) {
            return redirect()->route('customer.dashboard')->with('info', 'Pesanan ini sudah dibayar atau tidak dalam status pending.');
        }

        return view('customer.order.payment', compact('order'));
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending' || $order->payment_proof) {
            return redirect()->route('customer.dashboard')->with('info', 'Pesanan ini sudah dibayar.');
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'status'        => 'processing',
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan segera memproses pesanan Anda.');
    }
}