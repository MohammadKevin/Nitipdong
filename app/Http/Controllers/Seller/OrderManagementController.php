<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderManagementController extends Controller
{
    public function index(Request $request): View
    {
        $store = Auth::user()->store;

        $query = Order::with(['user', 'orderItems.product', 'complaint'])
            ->where('store_id', $store?->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403, 'Akses tidak sah ke pesanan toko lain.');
        }

        $order->loadMissing(['user', 'orderItems.product', 'complaint', 'store', 'courier']);

        return view('seller.orders.show', compact('order'));
    }

    public function processOrder(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403, 'Akses tidak sah.');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Status pesanan tidak dapat diproses.');
        }

        $order->update([
            'status'          => 'processing',
            'shipping_status' => $order->shipping_status ?: 'pickup_pending',
        ]);

        AppNotification::send(
            $order->user_id,
            'Pesanan Sedang Diproses',
            "Pesanan #{$order->invoice_number} sedang disiapkan oleh penjual.",
            'order',
            route('customer.dashboard')
        );

        return back()->with('success', 'Pesanan berhasil dikonfirmasi dan sedang diproses.');
    }

    public function shipOrder(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($order->status !== 'processing') {
            return back()->with('error', 'Hanya pesanan yang sedang diproses yang dapat dikirim.');
        }

        $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $trackingNo = $request->tracking_number ?: ($order->tracking_number ?: ('NDX-' . date('Ymd') . '-' . strtoupper(Str::random(8))));

        $order->update([
            'status'          => 'shipped',
            'tracking_number' => $trackingNo,
            'shipping_status' => 'picked_up',
        ]);

        AppNotification::send(
            $order->user_id,
            'Pesanan Sedang Dikirim',
            "Pesanan #{$order->invoice_number} telah dikirim via NitipDongExpress dengan nomor resi: {$trackingNo}.",
            'order',
            route('customer.dashboard')
        );

        return back()->with('success', "Pesanan berhasil ditandai telah dikirim dengan nomor resi: {$trackingNo}.");
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403, 'Akses tidak sah.');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Pesanan yang sudah dikirim atau selesai tidak dapat dibatalkan.');
        }

        $reason = Str::limit(trim($request->input('reason', 'Dibatalkan oleh penjual.')), 500);

        DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => 'cancelled',
            ]);

            // Restore stocks and sold count
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                }
            }

            // Restore voucher quota
            if ($order->voucher_code) {
                $voucher = Voucher::where('code', $order->voucher_code)->first();
                if ($voucher) {
                    $voucher->increment('quota');
                }
            }

            AppNotification::send(
                $order->user_id,
                'Pesanan Dibatalkan Penjual',
                "Pesanan #{$order->invoice_number} telah dibatalkan oleh penjual. Alasan: {$reason}",
                'order',
                route('customer.dashboard')
            );
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dipulihkan.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($request->isMethod('get')) {
            return redirect()->route('seller.orders.show', $order);
        }

        $request->validate([
            'status'          => ['required', 'in:pending,processing,shipped,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($newStatus === 'cancelled') {
            return $this->cancelOrder($request, $order);
        }

        DB::transaction(function () use ($order, $request, $oldStatus, $newStatus) {
            $trackingNo = $request->tracking_number ?: $order->tracking_number;
            if ($newStatus === 'shipped' && empty($trackingNo)) {
                $trackingNo = 'NDX-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }

            $updates = [
                'status'          => $newStatus,
                'tracking_number' => $trackingNo,
            ];

            if ($newStatus === 'shipped') {
                $updates['shipping_status'] = 'picked_up';
            } elseif ($newStatus === 'completed') {
                $updates['shipping_status'] = 'delivered';
                if (!$order->completed_at) {
                    $updates['completed_at'] = now();
                }

                // Credit balance once only
                if (!$order->seller_credited_at) {
                    $sellerEarnings = round($order->total_amount * 0.85);
                    $order->store->increment('balance', $sellerEarnings);
                    $updates['seller_credited_at'] = now();
                }
            }

            $order->update($updates);

            // Notify buyer about status changes
            if ($newStatus === 'shipped') {
                AppNotification::send(
                    $order->user_id,
                    'Pesanan Sedang Dikirim',
                    "Pesanan #{$order->invoice_number} telah dikirim dengan nomor resi: {$trackingNo}.",
                    'order',
                    route('customer.dashboard')
                );
            } elseif ($newStatus === 'processing' && $oldStatus === 'pending') {
                AppNotification::send(
                    $order->user_id,
                    'Pesanan Sedang Diproses',
                    "Pesanan #{$order->invoice_number} sedang disiapkan oleh penjual.",
                    'order',
                    route('customer.dashboard')
                );
            } elseif ($newStatus === 'completed') {
                AppNotification::send(
                    $order->user_id,
                    'Pesanan Telah Selesai',
                    "Pesanan #{$order->invoice_number} telah ditandai selesai. Terima kasih telah berbelanja di NitipDong!",
                    'order',
                    route('customer.dashboard')
                );
            }
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}

