<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierApiController extends Controller
{
    /**
     * Get orders ready for delivery or completed.
     */
    public function index(Request $request): JsonResponse
    {
        // Get all orders in system with status processing, shipped, or completed
        $orders = Order::with(['orderItems.product.store', 'user'])
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->latest()
            ->get();

        $formatted = $orders->map(function ($order) {
            $firstItem = $order->orderItems->first();
            return [
                'id'              => $order->id,
                'invoice_number'  => $order->invoice_number,
                'order_number'    => $order->invoice_number,
                'total_amount'    => (float) $order->total_amount,
                'status'          => $order->status,
                'status_label'    => ucfirst($order->status),
                'shipping_address'=> $order->shipping_address ?? 'Alamat tidak diisi',
                'courier'         => $order->shipping_courier ?? 'J&T Express',
                'tracking_number' => $order->tracking_number,
                'recipient_name'  => $order->user->name ?? 'Pelanggan',
                'recipient_phone' => $order->user->phone ?? '08123456789',
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'items_count'     => $order->orderItems->count(),
                'first_product'   => $firstItem?->product ? [
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
        ]);
    }

    /**
     * Mark order as Picked Up / Shipped.
     */
    public function pickup(Request $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'processing') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan yang sedang diproses yang dapat diambil.',
            ], 422);
        }

        $trackingNo = 'TRK-' . strtoupper(substr(md5($order->id . time()), 0, 10));

        $order->update([
            'status'          => 'shipped',
            'tracking_number' => $trackingNo,
        ]);

        try {
            AppNotification::create([
                'user_id' => $order->user_id,
                'title'   => 'Paket Sedang Dikirim 🚚',
                'message' => "Pesanan #{$order->invoice_number} telah diserahkan ke kurir dan sedang dalam perjalanan. Lacak pengiriman Anda.",
                'link'    => route('customer.dashboard'),
            ]);
        } catch (\Exception $e) {}

        return response()->json([
            'success'         => true,
            'message'         => 'Pesanan berhasil diambil & dalam status pengiriman!',
            'status'          => 'shipped',
            'tracking_number' => $trackingNo,
        ]);
    }

    /**
     * Mark order as Delivered / Completed.
     */
    public function deliver(Request $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan yang sedang dikirim yang dapat diselesaikan.',
            ], 422);
        }

        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        try {
            AppNotification::create([
                'user_id' => $order->user_id,
                'title'   => 'Paket Tiba di Tujuan 🎉',
                'message' => "Hore! Pesanan #{$order->invoice_number} telah berhasil dikirim ke alamat Anda.",
                'link'    => route('customer.dashboard'),
            ]);
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Pengiriman pesanan berhasil diselesaikan!',
            'status'  => 'completed',
        ]);
    }
}
