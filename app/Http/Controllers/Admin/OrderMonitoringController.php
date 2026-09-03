<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        if ($order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        if ($order->status === Order::STATUS_COMPLETED) {
            return back()->with('error', 'Pesanan yang sudah selesai tidak dapat dibatalkan.');
        }

        $reason = 'Dibatalkan Admin: ' . $request->reason;

        try {
            WalletService::refundAndCancelOrder($order, $reason);

            return back()->with('success', "Pesanan #{$order->invoice_number} berhasil dibatalkan oleh admin dan dana/stok telah diproses.");
        } catch (\DomainException $de) {
            return back()->with('error', $de->getMessage());
        } catch (\Throwable $e) {
            Log::error('Admin cancel order error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }
}
