<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WalletApiController extends Controller
{
    /**
     * Get customer wallet balance and transaction history.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $cacheKey = "wallet_data_{$user->id}";

        $walletData = Cache::get($cacheKey, [
            'balance'      => 250000.0,
            'points'       => 1250,
            'is_active'    => true,
            'transactions' => [
                [
                    'id'          => 'TXN-984210',
                    'type'        => 'topup',
                    'title'       => 'Top Up Saldo NitipPay',
                    'amount'      => 200000.0,
                    'status'      => 'success',
                    'date'        => now()->subDays(2)->format('d M Y, H:i'),
                    'description' => 'Top Up via QRIS Instant',
                ],
                [
                    'id'          => 'TXN-981244',
                    'type'        => 'payment',
                    'title'       => 'Pembayaran Pesanan',
                    'amount'      => -75000.0,
                    'status'      => 'success',
                    'date'        => now()->subDays(5)->format('d M Y, H:i'),
                    'description' => 'Pembayaran Pesanan #INV-882190',
                ],
                [
                    'id'          => 'TXN-975312',
                    'type'        => 'cashback',
                    'title'       => 'Bonus Cashback Belanja',
                    'amount'      => 25000.0,
                    'status'      => 'success',
                    'date'        => now()->subDays(7)->format('d M Y, H:i'),
                    'description' => 'Cashback Promo Gajian NitipDong',
                ],
            ],
        ]);

        return response()->json([
            'success' => true,
            'data'    => $walletData,
        ]);
    }

    /**
     * Top up customer wallet balance.
     */
    public function topUp(Request $request): JsonResponse
    {
        $request->validate([
            'amount'         => ['required', 'numeric', 'min:10000', 'max:10000000'],
            'payment_method' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $amount = (float) $request->amount;
        $method = $request->input('payment_method', 'QRIS Instant');
        $cacheKey = "wallet_data_{$user->id}";

        $walletData = Cache::get($cacheKey, [
            'balance'      => 250000.0,
            'points'       => 1250,
            'is_active'    => true,
            'transactions' => [],
        ]);

        $newBalance = $walletData['balance'] + $amount;
        $newTxn = [
            'id'          => 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type'        => 'topup',
            'title'       => 'Top Up Saldo NitipPay',
            'amount'      => $amount,
            'status'      => 'success',
            'date'        => now()->format('d M Y, H:i'),
            'description' => "Top Up via {$method}",
        ];

        array_unshift($walletData['transactions'], $newTxn);
        $walletData['balance'] = $newBalance;
        $walletData['points'] += (int) ($amount / 1000);

        Cache::forever($cacheKey, $walletData);

        return response()->json([
            'success'     => true,
            'message'     => 'Top Up Saldo sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil!',
            'new_balance' => $newBalance,
            'transaction' => $newTxn,
        ]);
    }
}
