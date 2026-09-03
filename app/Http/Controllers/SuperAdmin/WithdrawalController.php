<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $query = Withdrawal::with('store.user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate(15)->withQueryString();

        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $approvedCount = Withdrawal::where('status', 'approved')->count();
        $rejectedCount = Withdrawal::where('status', 'rejected')->count();
        $totalPaidOut = Withdrawal::where('status', 'approved')->sum('amount');

        return view('super_admin.withdrawals.index', compact(
            'withdrawals',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalPaidOut'
        ));
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
            'proof'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $proofUrl = $withdrawal->proof_url;
        if ($request->hasFile('proof')) {
            $proofUrl = $request->file('proof')->store('payout-proofs', 'public');
        }

        $withdrawal->update([
            'status'      => 'approved',
            'admin_note'  => $request->admin_note,
            'proof_url'   => $proofUrl,
            'approved_at' => now(),
        ]);

        // Notify store owner
        if ($withdrawal->store && $withdrawal->store->user) {
            AppNotification::send(
                $withdrawal->store->user_id,
                'Penarikan Dana Berhasil Ditransfer',
                "Penarikan dana sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah berhasil diproses ke rekening {$withdrawal->bank_name} ({$withdrawal->account_number}).",
                'wallet',
                route('seller.wallet.index')
            );
        }

        return back()->with('success', 'Penarikan dana berhasil disetujui & ditandai selesai transfer.');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ], [
            'admin_note.required' => 'Alasan penolakan penarikan dana wajib dicantumkan.',
        ]);

        DB::transaction(function () use ($withdrawal, $request) {
            // Return balance back to store
            $withdrawal->store->increment('balance', $withdrawal->amount);

            $withdrawal->update([
                'status'     => 'rejected',
                'admin_note' => $request->admin_note,
            ]);

            // Notify store owner
            if ($withdrawal->store && $withdrawal->store->user) {
                AppNotification::send(
                    $withdrawal->store->user_id,
                    'Penarikan Dana Ditolak',
                    "Permohonan penarikan dana sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " ditolak: {$request->admin_note}. Saldo telah dikembalikan ke dompet toko Anda.",
                    'wallet',
                    route('seller.wallet.index')
                );
            }
        });

        return back()->with('success', 'Penarikan dana telah ditolak dan saldo berhasil dikembalikan ke toko.');
    }
}
