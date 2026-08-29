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
        $hubLat = $baseLat - 0.010;
        $hubLng = $baseLng - 0.010;

        // Buyer Destination Location (Rumah Pembeli)
        $destLat = $baseLat + 0.040;
        $destLng = $baseLng + 0.045;

        // Use real store coordinates if available
        if ($order->store && (float) $order->store->latitude != 0 && (float) $order->store->longitude != 0) {
            $originLat = (float) $order->store->latitude;
            $originLng = (float) $order->store->longitude;
        }

        // Use real saved GPS Pinpoint coordinates if available
        $userAddress = \App\Models\UserAddress::where('user_id', $order->user_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('is_default')
            ->first();

        if ($userAddress && (float) $userAddress->latitude != 0 && (float) $userAddress->longitude != 0) {
            $destLat = (float) $userAddress->latitude;
            $destLng = (float) $userAddress->longitude;
            if (!$order->store || !(float) $order->store->latitude) {
                $originLat = $destLat - 0.055;
                $originLng = $destLng - 0.045;
            }
            $hubLat = $destLat - 0.020;
            $hubLng = $destLng - 0.015;
        }

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

        $warehouse = $order->warehouse ?: \App\Models\Warehouse::findNearestForCity($order->shipping_address ?? $order->store?->city);
        if ($warehouse) {
            $hubLat = (float) $warehouse->lat;
            $hubLng = (float) $warehouse->lng;
        }

        // Use real assigned courier if present, otherwise fallback to NDX courier
        if ($order->courier) {
            $courier = [
                'name'  => $order->courier->name,
                'plate' => 'L 4242 NK',
                'phone' => $order->courier->phone ?? '0812-3456-7890',
                'exp'   => 'NitipDongExpress (NDX)',
            ];
        } else {
            $courierDrivers = [
                ['name' => 'Mas Kevin (Kurir NDX)', 'plate' => 'L 4242 NK', 'phone' => '0812-3456-7890', 'exp' => 'NitipDongExpress (NDX Reguler)'],
                ['name' => 'Budi Santoso', 'plate' => 'B 4821 NDX', 'phone' => '0812-8921-3829', 'exp' => 'NitipDongExpress (NDX Same Day)'],
                ['name' => 'Rian Pratama', 'plate' => 'B 6291 NDX', 'phone' => '0857-1928-4721', 'exp' => 'NitipDongExpress (NDX Express)'],
            ];
            $courier = $courierDrivers[$seed % count($courierDrivers)];
        }

        // Generate detailed checkpoint logs
        $checkpoints = [];

        if ($order->status === 'completed' || $order->shipping_status === 'delivered') {
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
                'title'     => 'Kurir NDX (' . $courier['name'] . ') sedang mengantar paket ke alamat penerima (Last-Mile)',
                'location'  => ($warehouse?->name ?? 'Gudang NDX Regional') . ' → ' . ($order->user->name ?? 'Penerima'),
                'time'      => now()->subMinutes(25)->format('d M Y, H:i'),
                'icon'      => 'fa-motorcycle',
                'status'    => 'done',
                'is_current'=> $order->status === 'shipped',
            ];
            $checkpoints[] = [
                'title'     => 'Paket telah tiba di ' . ($warehouse?->name ?? 'Gudang Hub DC NitipDongExpress') . ' dan telah disortir',
                'location'  => $warehouse?->address ?? 'Hub Distribusi NDX',
                'time'      => now()->subHours(2)->format('d M Y, H:i'),
                'icon'      => 'fa-warehouse',
                'status'    => 'done',
                'is_current'=> false,
            ];
            $checkpoints[] = [
                'title'     => 'Kurir NDX berhasil menjemput paket dari toko penjual (Pick-up)',
                'location'  => $order->store->name ?? 'Toko Penjual',
                'time'      => now()->subHours(4)->format('d M Y, H:i'),
                'icon'      => 'fa-box-open',
                'status'    => 'done',
                'is_current'=> false,
            ];
        }

        if (in_array($order->status, ['processing', 'shipped', 'completed'])) {
            $checkpoints[] = [
                'title'     => 'Pesanan sedang dikemas & dipacking oleh toko penjual',
                'location'  => $order->store->name ?? 'Toko Official',
                'time'      => $order->created_at->addMinutes(15)->format('d M Y, H:i'),
                'icon'      => 'fa-boxes-packing',
                'status'    => 'done',
                'is_current'=> $order->status === 'processing',
            ];
        }

        $checkpoints[] = [
            'title'     => 'Pesanan Berhasil Dibuat & Pembayaran Terkonfirmasi',
            'location'  => 'Sistem NitipDong Marketplace',
            'time'      => $order->created_at->format('d M Y, H:i'),
            'icon'      => 'fa-receipt',
            'status'    => 'done',
            'is_current'=> $order->status === 'pending',
        ];

        // Estimated arrival
        $estimatedTime = match ($order->status) {
            'completed'  => 'Pesanan Selesai Diterima',
            'shipped'    => 'Hari ini, estimasi tiba pukul ' . now()->addMinutes(35)->format('H:i') . ' WIB',
            'processing' => 'Estimasi tiba dalam 1-2 hari kerja (via NDX)',
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
