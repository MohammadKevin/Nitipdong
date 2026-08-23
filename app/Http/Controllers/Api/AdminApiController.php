<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    private function checkAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            return false;
        }
        return true;
    }

    /**
     * 1. Dashboard Ringkasan Metrik Platform untuk Admin & Super Admin
     */
    public function dashboard(Request $request): JsonResponse
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
                'total_gmv'          => (float) $totalGmv,
                'total_users'        => $totalUsers,
                'total_customers'    => $totalCustomers,
                'total_sellers'      => $totalSellers,
                'total_couriers'     => $totalCouriers,
                'total_stores'       => $totalStores,
                'total_orders'       => $totalOrders,
                'recent_orders'      => $recentOrders,
                'pending_stores'     => Store::where('is_active', false)->count(),
                'total_products'     => Product::count(),
                'total_categories'   => Category::count(),
                'active_flash_sales' => FlashSale::count(),
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
    public function stores(Request $request): JsonResponse
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
     * 5. Get Live Maintenance Status
     */
    public function getMaintenanceStatus(): JsonResponse
    {
        $webFile = storage_path('framework/maintenance_web.json');
        $mobileFile = storage_path('framework/maintenance_app.json');
        $downFile = storage_path('framework/down');

        $isWebDown = file_exists($webFile) || env('APP_WEB_MAINTENANCE', false);
        $isMobileDown = file_exists($mobileFile) || env('APP_MOBILE_MAINTENANCE', false);
        $isAllDown = file_exists($downFile);

        $msgData = [];
        if (file_exists($mobileFile)) {
            $msgData = json_decode(@file_get_contents($mobileFile), true) ?: [];
        } elseif (file_exists($webFile)) {
            $msgData = json_decode(@file_get_contents($webFile), true) ?: [];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'web_maintenance'    => (bool) $isWebDown,
                'mobile_maintenance' => (bool) $isMobileDown,
                'full_lockdown'      => (bool) $isAllDown,
                'title'              => $msgData['title'] ?? 'Mode Pemeliharaan Sistem 🛠️',
                'message'            => $msgData['message'] ?? 'Sistem sedang dalam optimalisasi server terjadwal.',
            ],
        ]);
    }

    /**
     * 6. Toggle Maintenance Mode Platform (Web, Mobile, or All)
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        $target = $request->input('target', 'all'); // 'web', 'mobile', 'all'
        $isDown = $request->boolean('is_down');
        $title  = $request->input('title', 'Mode Pemeliharaan Sistem 🛠️');
        $msg    = $request->input('message', 'Sistem sedang dalam optimalisasi server terjadwal.');

        $payload = json_encode([
            'title'     => $title,
            'message'   => $msg,
            'time'      => time(),
            'author'    => Auth::user()?->name ?? 'Super Admin',
        ], JSON_PRETTY_PRINT);

        $webFile = storage_path('framework/maintenance_web.json');
        $mobileFile = storage_path('framework/maintenance_app.json');
        $downFile = storage_path('framework/down');

        if ($target === 'web' || $target === 'all') {
            if ($isDown) {
                @file_put_contents($webFile, $payload);
            } else {
                if (file_exists($webFile)) @unlink($webFile);
            }
        }

        if ($target === 'mobile' || $target === 'all') {
            if ($isDown) {
                @file_put_contents($mobileFile, $payload);
            } else {
                if (file_exists($mobileFile)) @unlink($mobileFile);
            }
        }

        if ($target === 'all') {
            if ($isDown) {
                @file_put_contents($downFile, $payload);
            } else {
                if (file_exists($downFile)) @unlink($downFile);
            }
        }

        cache()->forever('system_maintenance_mode', $isDown);

        $targetLabel = match ($target) {
            'web'    => 'Website',
            'mobile' => 'Aplikasi Mobile',
            default  => 'Seluruh Platform (Web & Mobile)',
        };

        return response()->json([
            'success'            => true,
            'message'            => $isDown ? "Mode pemeliharaan $targetLabel berhasil DIAKTIFKAN 🛠️." : "Mode pemeliharaan $targetLabel berhasil DINONAKTIFKAN (Live Normal) 🟢.",
            'web_maintenance'    => file_exists($webFile),
            'mobile_maintenance' => file_exists($mobileFile),
            'full_lockdown'      => file_exists($downFile),
        ]);
    }
}
