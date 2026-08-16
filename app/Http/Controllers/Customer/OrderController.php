<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    // Halaman konfirmasi checkout
    public function checkout(): View|RedirectResponse
    {
        $carts = Cart::with('product.store')
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $totalPrice = $carts->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('customer.order.checkout', compact('carts', 'totalPrice'));
    }

    // Proses pembuatan pesanan
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'min:15'],
        ]);

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Kelompokkan item berdasarkan Toko (Multi-Vendor Order)
        $groupedByStore = $carts->groupBy(fn ($item) => $item->product->store_id);

        DB::transaction(function () use ($groupedByStore, $request) {
            foreach ($groupedByStore as $storeId => $items) {
                $totalAmount = $items->sum(fn ($i) => $i->product->price * $i->quantity);

                // Buat Pesanan Utama
                $order = Order::create([
                    'invoice_number'   => 'INV-' . strtoupper(Str::random(10)),
                    'user_id'          => Auth::id(),
                    'store_id'         => $storeId,
                    'total_amount'     => $totalAmount,
                    'status'           => 'pending',
                    'shipping_address' => $request->shipping_address,
                ]);

                // Simpan detail item & kurangi stok
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->price,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                }
            }

            // Kosongkan keranjang belanja
            Cart::where('user_id', Auth::id())->delete();
        });

        return redirect()->route('customer.dashboard')->with('success', 'Pesanan berhasil dibuat! Seller akan segera memproses barang Anda.');
    }

    // Halaman instruksi transfer dan upload bukti pembayaran
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

    // Konfirmasi dan upload bukti pembayaran
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