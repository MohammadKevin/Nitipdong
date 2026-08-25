<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderMonitoringController extends Controller
{
    /**
     * Display real-time order monitoring dashboard.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');

        $query = Order::with(['user', 'store', 'orderItems.product', 'complaint'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Real-time KPI counts
        $totalOrders      = Order::count();
        $processingOrders = Order::whereIn('status', ['paid', 'processing'])->count();
        $shippedOrders    = Order::where('status', 'shipped')->count();
        $completedOrders  = Order::where('status', 'completed')->count();
        $cancelledOrders  = Order::where('status', 'cancelled')->count();

        return view('admin.orders.index', compact(
            'orders',
            'status',
            'search',
            'totalOrders',
            'processingOrders',
            'shippedOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }

    /**
     * Cancel stalled or fraudulent order by admin.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Wajib mengisi alasan pembatalan pesanan.',
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'status' => 'cancelled',
                'notes'  => ($order->notes ? $order->notes . ' | ' : '') . 'Dibatalkan Admin: ' . $request->reason,
            ]);

            // Restore stock
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Notify buyer
            AppNotification::send(
                $order->user_id,
                'Pesanan Dibatalkan oleh Admin',
                "Pesanan #{$order->invoice_number} telah dibatalkan oleh Admin Operasional. Alasan: {$request->reason}",
                'order',
                route('customer.dashboard')
            );

            // Notify seller if applicable
            if ($order->store && $order->store->user_id) {
                AppNotification::send(
                    $order->store->user_id,
                    'Pesanan Dibatalkan oleh Admin',
                    "Pesanan #{$order->invoice_number} pada toko Anda telah dibatalkan oleh Admin Operasional. Alasan: {$request->reason}",
                    'order',
                    route('seller.orders.index')
                );
            }
        });

        return back()->with('success', "Pesanan #{$order->invoice_number} berhasil dibatalkan oleh admin.");
    }
}
