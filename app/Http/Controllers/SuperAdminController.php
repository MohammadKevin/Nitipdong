<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $grossVolume = (float) Order::where('status', 'completed')->sum('total_amount');
        $totalKeuntunganPlatform = round($grossVolume * 0.05);

        $activeOrders = Order::whereIn('status', ['pending', 'processing', 'shipped'])->count();
        $pesananBaru = Order::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();
        $totalPengguna = User::count();
        $totalToko = Store::count();
        $totalPesanan = Order::count();
        $recentOrders = Order::with(['user', 'store'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('super_admin.dashboard', compact(
            'grossVolume',
            'totalKeuntunganPlatform',
            'activeOrders',
            'pesananBaru',
            'totalPengguna',
            'totalToko',
            'totalPesanan',
            'recentOrders'
        ));
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        
        $usersQuery = User::latest();
        
        if ($search) {
            $usersQuery->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $usersQuery->paginate(10)->withQueryString();
        
        return view('super_admin.users.index', compact('users', 'search'));
    }

    public function stores(Request $request)
    {
        $search = $request->input('search');
        
        $storesQuery = Store::with('user')->latest();
        
        if ($search) {
            $storesQuery->where('name', 'like', "%{$search}%");
        }
        
        $stores = $storesQuery->paginate(10)->withQueryString();
        
        return view('super_admin.stores.index', compact('stores', 'search'));
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
        $period = $request->query('period', 'week');

        [$labels, $data] = match ($period) {
            'day'   => $this->chartDaily(),
            'week'  => $this->chartWeekly(),
            'month' => $this->chartMonthly(),
            'year'  => $this->chartYearly(),
            default => $this->chartWeekly(),
        };

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    private function chartDaily(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i));
        $labels = $days->map(fn ($d) => $d->translatedFormat('D'))->toArray();

        $totals = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $data = $days->map(fn ($d) => round(((float) ($totals[$d->format('Y-m-d')] ?? 0)) * 0.05))->toArray();

        return [$labels, $data];
    }

    private function chartWeekly(): array
    {
        $labels = [];
        $data = [];
        $i = 1;
        $weekStart = now()->startOfMonth()->startOfWeek();

        while ($weekStart->lte(now())) {
            $weekEnd = $weekStart->copy()->endOfWeek()->min(now());
            $labels[] = 'M' . $i;
            $volume = (float) Order::where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');
            $data[] = round($volume * 0.05);
            $weekStart = $weekStart->copy()->addWeek();
            $i++;
        }

        return [$labels, $data];
    }

    private function chartMonthly(): array
    {
        $labels = [];
        $data = [];

        for ($m = 1; $m <= now()->month; $m++) {
            $labels[] = Carbon::create(now()->year, $m, 1)->translatedFormat('M');
            $volume = (float) Order::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $m)
                ->sum('total_amount');
            $data[] = round($volume * 0.05);
        }

        return [$labels, $data];
    }

    private function chartYearly(): array
    {
        $years = range(now()->year - 4, now()->year);
        $labels = array_map('strval', $years);
        $data = array_map(
            fn ($y) => round(((float) Order::where('status', 'completed')->whereYear('created_at', $y)->sum('total_amount')) * 0.05),
            $years
        );

        return [$labels, $data];
    }
}