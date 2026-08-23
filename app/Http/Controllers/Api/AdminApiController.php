<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{
    /**
     * 1. Dashboard Ringkasan Metrik Platform untuk Admin & Super Admin
     */
    public function dashboard(): JsonResponse
    {
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalCouriers = User::where('role', 'courier')->count();
        $totalStores = Store::count();
        $totalOrders = Order::count();
        $totalGmv = Order::where('status', 'completed')->sum('total_amount');

        $recentOrders = Order::with(['user', 'store'])->latest()->take(10)->get()->map(function ($order) {
            return [
                'id'             => $order->id,
                'invoice_number' => $order->invoice_number,
                'status'         => $order->status,
                'total_amount'   => (float) $order->total_amount,
                'customer_name'  => $order->user?->name ?? 'Pembeli',
                'store_name'     => $order->store?->name ?? 'Toko',
                'created_at'     => $order->created_at?->format('d M Y, H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'total_gmv'       => (float) $totalGmv,
                'total_users'     => $totalUsers,
                'total_customers' => $totalCustomers,
                'total_sellers'   => $totalSellers,
                'total_couriers'  => $totalCouriers,
                'total_stores'    => $totalStores,
                'total_orders'    => $totalOrders,
                'recent_orders'   => $recentOrders,
            ],
        ]);
    }

    /**
     * 2. Daftar Pengguna Platform
     */
    public function users(Request $request): JsonResponse
    {
        $role = $request->query('role');
        $query = User::latest();

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query->take(50)->get()->map(function ($u) {
            return [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'phone'      => $u->phone ?? '-',
                'role'       => $u->role,
                'created_at' => $u->created_at?->format('d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    /**
     * 3. Daftar Toko Platform
     */
    public function stores(): JsonResponse
    {
        $stores = Store::with('user')->latest()->take(50)->get()->map(function ($s) {
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'owner_name'  => $s->user?->name ?? 'Pemilik',
                'city'        => $s->city ?? 'Indonesia',
                'phone'       => $s->phone ?? '-',
                'is_active'   => (bool) ($s->is_active ?? true),
                'created_at'  => $s->created_at?->format('d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $stores,
        ]);
    }

    /**
     * 4. Toggle Status Toko (Aktif / Suspend)
     */
    public function toggleStoreStatus($id): JsonResponse
    {
        $store = Store::findOrFail($id);
        $store->is_active = !$store->is_active;
        $store->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Status toko berhasil diubah.',
            'is_active' => (bool) $store->is_active,
        ]);
    }

    /**
     * 5. Toggle Maintenance Mode Platform
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        $isDown = $request->boolean('is_down');
        // Save in cache or system flag
        cache()->forever('system_maintenance_mode', $isDown);

        return response()->json([
            'success'        => true,
            'message'        => $isDown ? 'Mode pemeliharaan diaktifkan.' : 'Sistem kembali normal (Live).',
            'is_maintenance' => $isDown,
        ]);
    }
}
