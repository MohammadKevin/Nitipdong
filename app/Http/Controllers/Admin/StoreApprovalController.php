<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StoreApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $pendingCount  = Store::where('status', 'pending')->count();
        $approvedCount = Store::where('status', 'approved')->count();
        $rejectedCount = Store::where('status', 'rejected')->count();

        $pendingStores = Store::with('user')
            ->where('status', 'pending')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.dashboard', compact('pendingCount', 'approvedCount', 'rejectedCount', 'pendingStores', 'search'));
    }

    public function approve(Store $store): RedirectResponse
    {
        DB::transaction(function () use ($store) {
            $store->update(['status' => 'approved']);
            $store->user->update(['role' => 'seller']);

            \App\Models\AppNotification::send(
                $store->user_id,
                'Pengajuan Toko Disetujui! 🎉',
                "Selamat! Pengajuan toko '{$store->name}' Anda telah disetujui. Anda kini dapat mulai berjualan di Seller Center.",
                'store',
                route('seller.dashboard')
            );
        });

        return back()->with('success', "Toko {$store->name} berhasil disetujui. Akun user ditingkatkan menjadi Seller.");
    }

    public function reject(Store $store): RedirectResponse
    {
        $store->update(['status' => 'rejected']);

        \App\Models\AppNotification::send(
            $store->user_id,
            'Pengajuan Toko Ditolak',
            "Mohon maaf, pengajuan toko '{$store->name}' Anda belum dapat disetujui. Silakan periksa kembali kelengkapan profil Anda.",
            'store',
            route('customer.dashboard')
        );

        return back()->with('success', "Pengajuan toko {$store->name} telah ditolak.");
    }
}
