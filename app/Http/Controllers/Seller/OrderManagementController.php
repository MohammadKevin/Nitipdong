<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderManagementController extends Controller
{
    public function index(): View
    {
        $store = Auth::user()->store;

        $orders = Order::with(['user', 'orderItems.product', 'complaint'])
            ->where('store_id', $store?->id)
            ->latest()
            ->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        $request->validate([
            'status'          => ['required', 'in:pending,processing,shipped,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($order, $request, $oldStatus, $newStatus) {
            $updates = [
                'status'          => $newStatus,
                'tracking_number' => $request->tracking_number ?: $order->tracking_number,
            ];

            if ($newStatus === 'completed' && !$order->completed_at) {
                $updates['completed_at'] = now();

                // If not yet credited, credit 95%
                if ($oldStatus !== 'completed') {
                    $sellerEarnings = round($order->total_amount * 0.95);
                    $order->store->increment('balance', $sellerEarnings);
                }
            }

            $order->update($updates);

            // Notify buyer about status changes
            if ($newStatus === 'shipped') {
                AppNotification::send(
                    $order->user_id,
                    'Pesanan Sedang Dikirim',
                    "Pesanan #{$order->invoice_number} telah dikirim" . ($request->tracking_number ? " dengan nomor resi: {$request->tracking_number}" : "") . ".",
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
                    "Pesanan #{$order->invoice_number} telah ditandai selesai. Terima kasih telah berbelanja di BelanjaIn!",
                    'order',
                    route('customer.dashboard')
                );
            }
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}