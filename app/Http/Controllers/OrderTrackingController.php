<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function show(Request $request, Order $order): View|JsonResponse
    {
        $user = Auth::user();
        $isCustomer = $order->user_id === $user->id;
        $isSeller = $user->store && $order->store_id === $user->store->id;
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);

        if (!$isCustomer && !$isSeller && !$isAdmin) {
            abort(403, 'Akses tidak sah untuk melacak pesanan ini.');
        }

        $order->load(['user', 'store', 'orderItems.product']);

        // Generate deterministic coordinates based on order ID so it's consistent for the same order
        $seed = crc32($order->invoice_number);
        mt_srand($seed);

        // Base anchor in Jakarta Jabodetabek / Indonesia urban area
        $baseLat = -6.1800 + (mt_rand(0, 80) / 1000);
        $baseLng = 106.8200 + (mt_rand(0, 80) / 1000);

        // Store Origin Location (Gudang Penjual)
        $originLat = $baseLat - 0.045;
        $originLng = $baseLng - 0.035;

        // Sorting Hub Location
        $hubLat = $baseLat - 0.015;
        $hubLng = $baseLng - 0.010;

        // Buyer Destination Location (Rumah Pembeli)
        $destLat = $baseLat + 0.040;
        $destLng = $baseLng + 0.045;

        // Progress based on order status
        $progressPercentage = match ($order->status) {
            'pending'    => 5,
            'processing' => 25,
            'shipped'    => 75,
            'completed'  => 100,
            'cancelled'  => 0,
            default      => 50,
        };

        // Live Courier Location (interpolate between Hub and Destination if shipped, or at destination if completed)
        if ($order->status === 'completed') {
            $courierLat = $destLat;
            $courierLng = $destLng;
        } elseif ($order->status === 'shipped') {
            // 75% towards destination with slight road jitter
            $courierLat = $hubLat + (($destLat - $hubLat) * 0.72) + 0.002;
            $courierLng = $hubLng + (($destLng - $hubLng) * 0.72) - 0.001;
        } elseif ($order->status === 'processing') {
            $courierLat = $originLat;
            $courierLng = $originLng;
        } else {
            $courierLat = $originLat;
            $courierLng = $originLng;
        }

        // Generate realistic courier data
        $courierDrivers = [
            ['name' => 'Budi Santoso', 'plate' => 'B 4821 KEV', 'phone' => '0812-8921-3829', 'exp' => 'BelanjaIn Express (Instant)'],
            ['name' => 'Rian Pratama', 'plate' => 'B 6291 TRZ', 'phone' => '0857-1928-4721', 'exp' => 'BelanjaIn Express (Reguler)'],
            ['name' => 'Ahmad Fauzi', 'plate' => 'B 3019 WQA', 'phone' => '0878-3921-8840', 'exp' => 'J&T Express Cargo'],
            ['name' => 'Hendra Setiawan', 'plate' => 'B 5512 PLM', 'phone' => '0813-7721-9042', 'exp' => 'SiCepat Reguler'],
        ];
        $courier = $courierDrivers[$seed % count($courierDrivers)];

        // Generate detailed checkpoint logs
        $checkpoints = [];

        if ($order->status === 'completed') {
            $checkpoints[] = [
                'title'     => 'Pesanan Telah Diterima oleh Pembeli',
                'location'  => $order->shipping_address ? explode("\n", $order->shipping_address)[0] : 'Alamat Tujuan',
                'time'      => $order->completed_at ? $order->completed_at->format('d M Y, H:i') : now()->format('d M Y, H:i'),
                'icon'      => 'fa-circle-check',
                'status'    => 'done',
                'is_current'=> true,
            ];
        }

        if (in_array($order->status, ['shipped', 'completed'])) {
            $checkpoints[] = [
                'title'     => 'Kurir sedang membawa paket menuju alamat penerima',
                'location'  => 'Hub Terakhir - Menuju ' . ($order->user->name ?? 'Penerima'),
                'time'      => now()->subMinutes(25)->format('d M Y, H:i'),
                'icon'      => 'fa-motorcycle',
                'status'    => 'done',
                'is_current'=> $order->status === 'shipped',
            ];
            $checkpoints[] = [
                'title'     => 'Paket telah tiba di Hub Transit Distribusi',
                'location'  => 'DC Sortir Logistik Barat',
                'time'      => now()->subHours(2)->format('d M Y, H:i'),
                'icon'      => 'fa-warehouse',
                'status'    => 'done',
                'is_current'=> false,
            ];
            $checkpoints[] = [
                'title'     => 'Paket diserahkan oleh penjual ke ekspedisi',
                'location'  => $order->store->name ?? 'Gudang Toko',
                'time'      => now()->subHours(5)->format('d M Y, H:i'),
                'icon'      => 'fa-box-open',
                'status'    => 'done',
                'is_current'=> false,
            ];
        }

        if (in_array($order->status, ['processing', 'shipped', 'completed'])) {
            $checkpoints[] = [
                'title'     => 'Pesanan sedang disiapkan & dipacking oleh penjual',
                'location'  => $order->store->name ?? 'Toko Official',
                'time'      => $order->created_at->addMinutes(15)->format('d M Y, H:i'),
                'icon'      => 'fa-boxes-packing',
                'status'    => 'done',
                'is_current'=> $order->status === 'processing',
            ];
        }

        $checkpoints[] = [
            'title'     => 'Pesanan Berhasil Dibuat & Pembayaran Terkonfirmasi',
            'location'  => 'Sistem BelanjaIn Marketplace',
            'time'      => $order->created_at->format('d M Y, H:i'),
            'icon'      => 'fa-receipt',
            'status'    => 'done',
            'is_current'=> $order->status === 'pending',
        ];

        // Estimated arrival
        $estimatedTime = match ($order->status) {
            'completed'  => 'Pesanan Selesai Diterima',
            'shipped'    => 'Hari ini, estimasi tiba pukul ' . now()->addMinutes(35)->format('H:i') . ' WIB',
            'processing' => 'Estimasi tiba besok (1-2 hari kerja)',
            default      => 'Menunggu konfirmasi pembayaran',
        };

        $trackingData = [
            'order'              => $order,
            'origin'             => ['lat' => $originLat, 'lng' => $originLng, 'label' => $order->store->name ?? 'Toko Penjual'],
            'hub'                => ['lat' => $hubLat, 'lng' => $hubLng, 'label' => 'Pusat Sortir Hub DC'],
            'destination'        => ['lat' => $destLat, 'lng' => $destLng, 'label' => $order->user->name ?? 'Alamat Pembeli'],
            'courier_pos'        => ['lat' => $courierLat, 'lng' => $courierLng],
            'courier'            => $courier,
            'progress'           => $progressPercentage,
            'estimated_time'     => $estimatedTime,
            'checkpoints'        => $checkpoints,
        ];

        if ($request->wantsJson()) {
            return response()->json($trackingData);
        }

        return view('orders.tracking', $trackingData);
    }
}
