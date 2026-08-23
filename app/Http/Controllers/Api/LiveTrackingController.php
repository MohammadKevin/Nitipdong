<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveTrackingController extends Controller
{
    /**
     * Ambil Data Live Tracking GPS & Timeline Pesanan untuk Pembeli
     */
    public function getLiveTracking(Request $request, $orderId): JsonResponse
    {
        $order = Order::with(['store', 'user', 'courier', 'orderItems.product'])->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Pastikan hanya pemilik pesanan, admin, atau kurir yang bisa melihat
        $user = Auth::user();
        if ($user && $order->user_id !== $user->id && $order->courier_id !== $user->id && !in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $courier = $order->courier;
        $store = $order->store;

        // Default Surabaya Map Coordinates
        $storeLat = -7.2575;
        $storeLng = 112.7521;
        $destLat = -7.2892;
        $destLng = 112.7344;

        // Hitung estimasi posisi kurir
        $courierLat = (float) ($order->courier_lat ?? $storeLat);
        $courierLng = (float) ($order->courier_lng ?? $storeLng);

        // Status Timeline Steps
        $timeline = [
            [
                'title'       => 'Pesanan Berhasil Dibuat',
                'description' => 'Menunggu verifikasi pembayaran.',
                'time'        => $order->created_at?->format('d M Y, H:i') . ' WIB',
                'is_done'     => true,
            ],
            [
                'title'       => 'Pembayaran Terverifikasi',
                'description' => 'Pembayaran lunas via ' . strtoupper($order->payment_method ?? 'QRIS'),
                'time'        => $order->created_at?->addMinutes(5)->format('d M Y, H:i') . ' WIB',
                'is_done'     => in_array($order->status, ['processing', 'shipped', 'completed']),
            ],
            [
                'title'       => 'Dikemas oleh Toko',
                'description' => 'Penjual sedang menyiapkan barang dan memeriksa kualitas produk.',
                'time'        => in_array($order->status, ['processing', 'shipped', 'completed']) ? $order->created_at?->addMinutes(20)->format('d M Y, H:i') . ' WIB' : null,
                'is_done'     => in_array($order->status, ['processing', 'shipped', 'completed']),
            ],
            [
                'title'       => 'Paket Diserahkan ke Kurir',
                'description' => $courier ? "Paket dijemput oleh Kurir {$courier->name} ({$order->shipping_courier})." : "Menunggu penjemputan kurir ({$order->shipping_courier}).",
                'time'        => in_array($order->status, ['shipped', 'completed']) ? $order->updated_at?->format('d M Y, H:i') . ' WIB' : null,
                'is_done'     => in_array($order->status, ['shipped', 'completed']),
            ],
            [
                'title'       => 'Dalam Perjalanan Menuju Alamat',
                'description' => 'Kurir sedang dalam perjalanan ke alamat tujuan Anda.',
                'time'        => in_array($order->status, ['shipped', 'completed']) ? ($order->courier_location_updated_at?->format('H:i') . ' WIB') : null,
                'is_done'     => in_array($order->status, ['shipped', 'completed']),
            ],
            [
                'title'       => 'Paket Telah Tiba & Diterima',
                'description' => $order->status === 'completed' ? ($order->delivery_notes ?? 'Pesanan telah diterima oleh pembeli.') : 'Estimasi tiba dalam 30-45 menit.',
                'time'        => $order->completed_at?->format('d M Y, H:i') . ' WIB',
                'is_done'     => $order->status === 'completed',
            ],
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'order_id'            => $order->id,
                'invoice_number'      => $order->invoice_number,
                'status'              => $order->status,
                'shipping_courier'    => $order->shipping_courier ?? 'NitipDong Express',
                'tracking_number'     => $order->tracking_number ?? ('NTP-' . strtoupper(substr(md5($order->id), 0, 8))),
                'courier_info'        => [
                    'id'               => $courier?->id,
                    'name'             => $courier?->name ?? 'Kurir Mitra NitipDong',
                    'phone'            => $courier?->phone ?? '081234567890',
                    'avatar'           => $courier?->avatar_url ?? null,
                    'rating'           => 4.95,
                    'vehicle'          => 'Honda Vario 160 (L 4242 NK)',
                    'is_assigned'      => (bool) $courier,
                ],
                'locations'           => [
                    'store' => [
                        'name'    => $store?->name ?? 'Toko NitipDong Official',
                        'address' => $store?->address ?? ($store?->city ?? 'Pusat Distribusi Toko'),
                        'lat'     => $storeLat,
                        'lng'     => $storeLng,
                    ],
                    'destination' => [
                        'recipient' => $order->user?->name ?? 'Penerima Paket',
                        'address'   => $order->shipping_address ?? 'Alamat Tujuan',
                        'lat'       => $destLat,
                        'lng'       => $destLng,
                    ],
                    'courier_live' => [
                        'lat'          => $courierLat,
                        'lng'          => $courierLng,
                        'updated_time' => $order->courier_location_updated_at?->diffForHumans() ?? 'Baru saja',
                    ],
                ],
                'delivery_proof_url'  => $order->delivery_proof_image ? asset('storage/' . $order->delivery_proof_image) : null,
                'delivery_notes'      => $order->delivery_notes,
                'timeline'            => $timeline,
            ],
        ]);
    }
}
