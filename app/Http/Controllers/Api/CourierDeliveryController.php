<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourierDeliveryController extends Controller
{
    /**
     * 1. Ambil Daftar Tugas Pengantaran (Aktif, Tersedia, & Riwayat Selesai)
     */
    public function index(Request $request): JsonResponse
    {
        $courier = Auth::user();
        $type = $request->query('type', 'active'); // 'active', 'available', 'completed'

        $query = Order::with(['store', 'user', 'orderItems.product']);

        if ($type === 'available') {
            // Pesanan siap diantar dari toko yang belum diambil kurir
            $orders = $query->where('status', 'processing')
                ->whereNull('courier_id')
                ->latest()
                ->take(30)
                ->get();
        } elseif ($type === 'completed') {
            // Riwayat antaran yang sudah diselesaikan kurir ini
            $orders = $query->where('courier_id', $courier->id)
                ->where('status', 'completed')
                ->latest('completed_at')
                ->take(30)
                ->get();
        } else {
            // Tugas aktif kurir yang sedang dalam perjalanan
            $orders = $query->where('courier_id', $courier->id)
                ->whereIn('status', ['shipped', 'processing'])
                ->latest()
                ->get();
        }

        $formatted = $orders->map(function (Order $order) {
            return $this->formatOrderForCourier($order);
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }

    /**
     * 2. Detail Tugas Pengantaran Spesifik
     */
    public function show(Request $request, $id): JsonResponse
    {
        $order = Order::with(['store', 'user', 'orderItems.product'])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatOrderForCourier($order),
        ]);
    }

    /**
     * 3. Kurir Menerima / Menjemput Paket dari Toko
     */
    public function acceptTask(Request $request, $id): JsonResponse
    {
        $courier = Auth::user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($order->courier_id && $order->courier_id !== $courier->id) {
            return response()->json(['success' => false, 'message' => 'Pesanan ini telah diambil oleh kurir lain.'], 400);
        }

        $lat = $request->input('lat', -7.2575);
        $lng = $request->input('lng', 112.7521);

        $order->update([
            'courier_id'                  => $courier->id,
            'status'                      => 'shipped',
            'courier_lat'                 => $lat,
            'courier_lng'                 => $lng,
            'courier_location_updated_at' => now(),
        ]);

        // Kirim Push Notification ke Pembeli
        PushNotificationService::sendOrderStatusNotification($order, 'shipped', "Kurir {$courier->name} sedang mengantar paket Anda ke alamat tujuan! 🚚");

        return response()->json([
            'success' => true,
            'message' => 'Tugas pengantaran berhasil diambil! Silakan menuju alamat pembeli.',
            'data'    => $this->formatOrderForCourier($order->fresh(['store', 'user', 'orderItems.product'])),
        ]);
    }

    /**
     * 4. Broadcast Update Koordinat GPS Kurir Secara Real-Time
     */
    public function updateGps(Request $request, $id): JsonResponse
    {
        $courier = Auth::user();
        $order = Order::where('id', $id)->where('courier_id', $courier->id)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Tugas pengantaran tidak ditemukan'], 404);
        }

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $order->update([
            'courier_lat'                 => $request->lat,
            'courier_lng'                 => $request->lng,
            'courier_location_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi GPS berhasil diperbarui',
            'data'    => [
                'lat'        => (float) $order->courier_lat,
                'lng'        => (float) $order->courier_lng,
                'updated_at' => $order->courier_location_updated_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * 5. Kurir Menyelesaikan Pengantaran (Upload Bukti Foto Serah Terima)
     */
    public function completeDelivery(Request $request, $id): JsonResponse
    {
        $courier = Auth::user();
        $order = Order::where('id', $id)->where('courier_id', $courier->id)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Tugas pengantaran tidak ditemukan'], 404);
        }

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $proofImagePath = $request->file('proof_image')->store('deliveries/proofs', 'public');
        } elseif ($request->filled('proof_image_base64')) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->proof_image_base64));
            $fileName = 'proof_' . time() . '_' . $order->id . '.jpg';
            Storage::disk('public')->put('deliveries/proofs/' . $fileName, $imageData);
            $proofImagePath = 'deliveries/proofs/' . $fileName;
        }

        $order->update([
            'status'               => 'completed',
            'completed_at'         => now(),
            'delivery_proof_image' => $proofImagePath,
            'delivery_notes'       => $request->input('notes', 'Paket telah diterima dengan baik oleh pembeli.'),
        ]);

        // Kirim Push Notification ke Pembeli
        PushNotificationService::sendOrderStatusNotification($order, 'completed', "Paket Anda telah diserahkan oleh kurir {$courier->name}. Terima kasih! 🎉");

        return response()->json([
            'success' => true,
            'message' => 'Pengantaran paket berhasil diselesaikan! Terima kasih atas dedikasi Anda. 📦🎉',
            'data'    => $this->formatOrderForCourier($order->fresh(['store', 'user', 'orderItems.product'])),
        ]);
    }

    /**
     * 6. Statistik & Pendapatan Kurir Hari Ini
     */
    public function statistics(Request $request): JsonResponse
    {
        $courier = Auth::user();

        $completedToday = Order::where('courier_id', $courier->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $activeDeliveries = Order::where('courier_id', $courier->id)
            ->where('status', 'shipped')
            ->count();

        $totalCompleted = Order::where('courier_id', $courier->id)
            ->where('status', 'completed')
            ->count();

        // Estimasi komisi kurir per paket (cth: Rp 12.000 / paket)
        $ratePerDelivery = 12000;
        $earningsToday = $completedToday * $ratePerDelivery;

        return response()->json([
            'success' => true,
            'data'    => [
                'courier_name'       => $courier->name,
                'courier_phone'      => $courier->phone ?? '081234567890',
                'active_tasks_count' => $activeDeliveries,
                'completed_today'    => $completedToday,
                'total_completed'    => $totalCompleted,
                'earnings_today'     => $earningsToday,
                'rate_per_delivery'  => $ratePerDelivery,
                'rating'             => 4.95,
            ],
        ]);
    }

    /**
     * Helper Format Data Order untuk Tampilan Kurir
     */
    private function formatOrderForCourier(Order $order): array
    {
        $store = $order->store;
        $user = $order->user;

        // Koordinat Toko (Pickup) & Pembeli (Dropoff)
        $pickupLat = -7.2575; // Surabaya default or store city coordinates
        $pickupLng = 112.7521;
        $dropoffLat = -7.2892;
        $dropoffLng = 112.7344;

        $items = $order->orderItems->map(function ($item) {
            return [
                'id'       => $item->id,
                'name'     => $item->product?->name ?? 'Produk Belanja',
                'quantity' => $item->quantity,
                'image'    => $item->product?->image_url ?? '',
                'price'    => (float) $item->price,
            ];
        });

        return [
            'id'                  => $order->id,
            'invoice_number'      => $order->invoice_number,
            'status'              => $order->status,
            'total_amount'        => (float) $order->total_amount,
            'shipping_courier'    => $order->shipping_courier ?? 'Kurir NitipDong Express',
            'shipping_address'    => $order->shipping_address ?? 'Alamat Penerima',
            'recipient_name'      => $user?->name ?? 'Pembeli NitipDong',
            'recipient_phone'     => $user?->phone ?? '081234567890',
            'store_name'          => $store?->name ?? 'Toko NitipDong Official',
            'store_address'       => $store?->address ?? ($store?->city ?? 'Pusat Distribusi Toko'),
            'store_phone'         => $store?->phone ?? '081298765432',
            'pickup_lat'          => $pickupLat,
            'pickup_lng'          => $pickupLng,
            'dropoff_lat'         => $dropoffLat,
            'dropoff_lng'         => $dropoffLng,
            'current_courier_lat' => (float) ($order->courier_lat ?? $pickupLat),
            'current_courier_lng' => (float) ($order->courier_lng ?? $pickupLng),
            'delivery_proof_url'  => $order->delivery_proof_image ? asset('storage/' . $order->delivery_proof_image) : null,
            'delivery_notes'      => $order->delivery_notes,
            'created_at'          => $order->created_at?->toIso8601String(),
            'items'               => $items,
        ];
    }

    /**
     * 6. Pendaftaran Customer Menjadi Mitra Kurir
     */
    public function registerCourier(Request $request): JsonResponse
    {
        $request->validate([
            'nik'          => 'required|string|max:30',
            'sim_number'   => 'required|string|max:30',
            'vehicle_type' => 'required|string|max:50',
            'plate_number' => 'required|string|max:20',
            'phone'        => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->role = 'courier';
        if ($request->filled('phone')) {
            $user->phone = $request->input('phone');
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Selamat! Pendaftaran Mitra Kurir berhasil. Akun Anda kini aktif sebagai Mitra Kurir NitipDong.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'phone' => $user->phone,
            ],
        ]);
    }
}
