<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $store = Auth::user()->store;
        if (!$store) {
            abort(403, 'Anda belum memiliki toko aktif.');
        }

        $grossSales = Order::where('store_id', $store->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $platformCommission = round($grossSales * 0.15);
        $totalNetEarnings = $grossSales - $platformCommission;

        $totalWithdrawn = Withdrawal::where('store_id', $store->id)
            ->where('status', 'approved')
            ->sum('amount');

        $pendingWithdrawal = Withdrawal::where('store_id', $store->id)
            ->where('status', 'pending')
            ->sum('amount');

        $withdrawals = Withdrawal::where('store_id', $store->id)
            ->latest()
            ->paginate(10);

        return view('seller.wallet.index', compact(
            'store',
            'grossSales',
            'platformCommission',
            'totalNetEarnings',
            'totalWithdrawn',
            'pendingWithdrawal',
            'withdrawals'
        ));
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $store = Auth::user()->store;
        if (!$store) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $request->validate([
            'amount'         => ['required', 'numeric', 'min:10000', 'max:' . $store->balance],
            'bank_name'      => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:100'],
        ], [
            'amount.min'     => 'Minimal penarikan dana adalah Rp 10.000.',
            'amount.max'     => 'Jumlah penarikan melebihi saldo dompet yang tersedia.',
            'bank_name.required' => 'Nama bank tujuan wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $store) {
            $lockedStore = \App\Models\Store::where('id', $store->id)->lockForUpdate()->first();
            if (!$lockedStore || $lockedStore->balance < $request->amount) {
                throw new \Exception('Saldo dompet tidak mencukupi untuk penarikan ini.');
            }

            // Deduct store balance
            $lockedStore->decrement('balance', $request->amount);

            // Update default bank info on store
            $lockedStore->update([
                'bank_name'           => $request->bank_name,
                'bank_account_number' => $request->account_number,
                'bank_account_holder' => $request->account_holder,
            ]);

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'store_id'       => $store->id,
                'amount'         => $request->amount,
                'bank_name'      => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
                'status'         => 'pending',
            ]);

            // Notify Super Admin
            $superAdmins = User::where('role', 'super_admin')->get();
            foreach ($superAdmins as $admin) {
                AppNotification::send(
                    $admin->id,
                    'Permohonan Penarikan Dana Baru',
                    "Toko {$store->name} mengajukan penarikan saldo sebesar Rp " . number_format($request->amount, 0, ',', '.'),
                    'wallet',
                    route('super_admin.withdrawals.index')
                );
            }
        });

        return back()->with('success', 'Permohonan penarikan dana berhasil dikirim! Admin akan segera memproses payout ke rekening Anda.');
    }
}
