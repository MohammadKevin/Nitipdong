<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $grossVolume = (float) Order::where('status', 'completed')->sum('total_amount');
        $totalKeuntunganPlatform = round($grossVolume * 0.15);

        // Status Counts
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::whereIn('status', ['cancelled', 'rejected'])->count();
        $activeOrders = $pendingOrders + $processingOrders + $shippedOrders;

        $pesananBaru = Order::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        // Ecosystem Counts
        $totalPengguna = User::count();
        $totalToko = Store::count();
        $pendingStoresCount = Store::where('status', 'pending')->count();
        $pendingWithdrawalsCount = class_exists(Withdrawal::class) ? Withdrawal::where('status', 'pending')->count() : 0;
        $totalPesanan = Order::count();

        // Growth & Financial Metrics
        $thisMonthGross = (float) Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $thisMonthProfit = round($thisMonthGross * 0.15);

        $lastMonthGross = (float) Order::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');
        
        $growthPercent = $lastMonthGross > 0 
            ? round((($thisMonthGross - $lastMonthGross) / $lastMonthGross) * 100, 1) 
            : ($thisMonthGross > 0 ? 100 : 0);

        $averageOrderValue = $completedOrders > 0 ? round($grossVolume / $completedOrders) : 0;

        $recentOrders = Order::with(['user', 'store'])
                            ->latest()
                            ->take(8)
                            ->get();

        return view('super_admin.dashboard', compact(
            'grossVolume',
            'totalKeuntunganPlatform',
            'activeOrders',
            'pesananBaru',
            'totalPengguna',
            'totalToko',
            'totalPesanan',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'completedOrders',
            'cancelledOrders',
            'pendingStoresCount',
            'pendingWithdrawalsCount',
            'thisMonthGross',
            'thisMonthProfit',
            'growthPercent',
            'averageOrderValue',
            'recentOrders'
        ));
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        
        $usersQuery = User::latest();
        
        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        if ($status === 'banned') {
            $usersQuery->where('is_banned', true);
        } elseif ($status === 'active') {
            $usersQuery->where('is_banned', false);
        }
        
        $users = $usersQuery->paginate(15)->withQueryString();

        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $sellerCount = User::where('role', 'seller')->count();
        $bannedCount = User::where('is_banned', true)->count();
        
        return view('super_admin.users.index', compact(
            'users', 
            'search', 
            'role', 
            'status', 
            'totalUsers', 
            'customerCount', 
            'sellerCount', 
            'bannedCount'
        ));
    }

    public function toggleBanUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat memblokir akun Anda sendiri.');
        }

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun Super Administrator tidak dapat diblokir.');
        }

        $user->update([
            'is_banned' => !$user->is_banned
        ]);

        $status = $user->is_banned ? 'diblokir / disuspend' : 'diaktifkan kembali';
        return back()->with('success', "Akun '{$user->name}' ({$user->email}) berhasil {$status}.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun Super Administrator tidak dapat dihapus.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun pengguna '{$userName}' telah berhasil dihapus dari sistem.");
    }

    public function stores(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $storesQuery = Store::with('user')->latest();
        
        if ($search) {
            $storesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status === 'active' || $status === 'approved') {
            $storesQuery->whereIn('status', ['approved', 'active']);
        } elseif ($status === 'pending') {
            $storesQuery->where('status', 'pending');
        } elseif ($status === 'banned' || $status === 'rejected') {
            $storesQuery->where('status', 'rejected');
        }
        
        $stores = $storesQuery->paginate(15)->withQueryString();

        $totalStores = Store::count();
        $activeStores = Store::whereIn('status', ['approved', 'active'])->count();
        $pendingStores = Store::where('status', 'pending')->count();
        $bannedStores = Store::where('status', 'rejected')->count();
        
        return view('super_admin.stores.index', compact(
            'stores', 
            'search', 
            'status', 
            'totalStores', 
            'activeStores', 
            'pendingStores', 
            'bannedStores'
        ));
    }

    public function toggleBan(Store $store)
    {
        if ($store->status === 'approved' || $store->status === 'pending') {
            $store->update(['status' => 'rejected']);
            $message = 'Toko berhasil diblokir/dinonaktifkan.';
        } else {
            $store->update(['status' => 'approved']);
            $message = 'Toko berhasil diaktifkan kembali.';
        }

        return back()->with('success', $message);
    }

    public function chartData(Request $request)
    {
        $period = $request->query('period', 'day');

        $result = match ($period) {
            'day'   => $this->chartDaily(),
            'week'  => $this->chartWeekly(),
            'month' => $this->chartMonthly(),
            'year'  => $this->chartYearly(),
            default => $this->chartDaily(),
        };

        return response()->json($result);
    }

    private function chartDaily(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i));
        $labels = $days->map(fn ($d) => $d->translatedFormat('d M'))->toArray();

        $totals = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $gmv = $days->map(fn ($d) => (float) ($totals[$d->format('Y-m-d')] ?? 0))->toArray();
        $commission = array_map(fn ($v) => round($v * 0.15), $gmv);

        return [
            'labels' => $labels,
            'data' => $commission,
            'gmv' => $gmv,
        ];
    }

    private function chartWeekly(): array
    {
        $labels = [];
        $commission = [];
        $gmv = [];
        $i = 1;
        $weekStart = now()->startOfMonth()->startOfWeek();

        while ($weekStart->lte(now())) {
            $weekEnd = $weekStart->copy()->endOfWeek()->min(now());
            $labels[] = 'Minggu ' . $i;
            $volume = (float) Order::where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');
            $gmv[] = $volume;
            $commission[] = round($volume * 0.15);
            $weekStart = $weekStart->copy()->addWeek();
            $i++;
        }

        return [
            'labels' => $labels,
            'data' => $commission,
            'gmv' => $gmv,
        ];
    }

    private function chartMonthly(): array
    {
        $labels = [];
        $commission = [];
        $gmv = [];

        for ($m = 1; $m <= now()->month; $m++) {
            $labels[] = Carbon::create(now()->year, $m, 1)->translatedFormat('M Y');
            $volume = (float) Order::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $m)
                ->sum('total_amount');
            $gmv[] = $volume;
            $commission[] = round($volume * 0.15);
        }

        return [
            'labels' => $labels,
            'data' => $commission,
            'gmv' => $gmv,
        ];
    }

    private function chartYearly(): array
    {
        $years = range(now()->year - 4, now()->year);
        $labels = array_map('strval', $years);
        $gmv = array_map(
            fn ($y) => (float) Order::where('status', 'completed')->whereYear('created_at', $y)->sum('total_amount'),
            $years
        );
        $commission = array_map(fn ($v) => round($v * 0.15), $gmv);

        return [
            'labels' => $labels,
            'data' => $commission,
            'gmv' => $gmv,
        ];
    }
}
