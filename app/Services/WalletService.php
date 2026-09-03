<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Credit earnings/funds to Store wallet balance with pessimistic concurrency locking.
     */
    public static function creditStore(Store $store, float $amount, string $description = ''): Store
    {
        if ($amount <= 0) {
            return $store;
        }

        return DB::transaction(function () use ($store, $amount, $description) {
            $lockedStore = Store::where('id', $store->id)->lockForUpdate()->firstOrFail();
            $lockedStore->increment('balance', $amount);

            Log::info("Wallet Credit: Store #{$store->id} ({$store->name}) +Rp {$amount}. Reason: {$description}");

            return $lockedStore;
        });
    }

    /**
     * Debit funds from Store wallet balance with pessimistic concurrency locking and balance checks.
     */
    public static function debitStore(Store $store, float $amount, string $description = ''): Store
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Jumlah penarikan harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($store, $amount, $description) {
            $lockedStore = Store::where('id', $store->id)->lockForUpdate()->firstOrFail();

            if ($lockedStore->balance < $amount) {
                throw new \DomainException('Saldo dompet tidak mencukupi untuk transaksi ini.');
            }

            $lockedStore->decrement('balance', $amount);

            Log::info("Wallet Debit: Store #{$store->id} ({$store->name}) -Rp {$amount}. Reason: {$description}");

            return $lockedStore;
        });
    }

    /**
     * Credit refund to customer wallet (NitipPay) safely.
     */
    public static function creditCustomerWallet(int $userId, float $amount, string $invoiceNumber, string $reason): void
    {
        if ($amount <= 0) {
            return;
        }

        $cacheKey = "wallet_data_{$userId}";
        $walletData = Cache::get($cacheKey, [
            'balance'      => 0.0,
            'points'       => 0,
            'is_active'    => true,
            'transactions' => [],
        ]);

        $newTxn = [
            'id'          => 'REF-' . strtoupper(substr(md5(uniqid((string) $userId, true)), 0, 8)),
            'type'        => 'refund',
            'title'       => 'Pengembalian Dana (Refund)',
            'amount'      => $amount,
            'status'      => 'success',
            'date'        => now()->format('d M Y, H:i'),
            'description' => "Refund Pesanan #{$invoiceNumber}: {$reason}",
        ];

        if (!isset($walletData['transactions']) || !is_array($walletData['transactions'])) {
            $walletData['transactions'] = [];
        }

        array_unshift($walletData['transactions'], $newTxn);
        $walletData['balance'] = (float) ($walletData['balance'] ?? 0) + $amount;

        Cache::forever($cacheKey, $walletData);
    }

    /**
     * Complete Order Cancellation and Automated Refund Process.
     * Restores stock atomically, recovers voucher quota, refunds customer, and adjusts seller balance.
     */
    public static function refundAndCancelOrder(Order $order, string $reason = 'Dibatalkan'): bool
    {
        return DB::transaction(function () use ($order, $reason) {
            // Pessimistic lock on the order
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === Order::STATUS_CANCELLED) {
                return true; // Already cancelled
            }

            if ($lockedOrder->status === Order::STATUS_COMPLETED) {
                throw new \DomainException("Pesanan #{$lockedOrder->invoice_number} sudah selesai dan tidak dapat dibatalkan.");
            }

            $wasPaidOrProcessing = in_array($lockedOrder->status, [Order::STATUS_PAID, Order::STATUS_PROCESSING], true);

            // 1. If seller was already credited, debit back from seller store
            if ($lockedOrder->seller_credited_at && $lockedOrder->store) {
                $sellerEarnings = round($lockedOrder->total_amount * 0.85);
                $lockedStore = Store::where('id', $lockedOrder->store_id)->lockForUpdate()->first();
                if ($lockedStore) {
                    $deductAmount = min($lockedStore->balance, $sellerEarnings);
                    if ($deductAmount > 0) {
                        $lockedStore->decrement('balance', $deductAmount);
                    }
                }
            }

            // 2. Automated customer refund if payment was made
            if ($wasPaidOrProcessing && $lockedOrder->total_amount > 0) {
                self::creditCustomerWallet(
                    $lockedOrder->user_id,
                    (float) $lockedOrder->total_amount,
                    $lockedOrder->invoice_number,
                    $reason
                );
            }

            // 3. Atomically restore product stocks with pessimistic locking
            foreach ($lockedOrder->orderItems as $item) {
                if ($item->product_id) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        $newSold = max(0, $product->sold_count - $item->quantity);
                        $product->update(['sold_count' => $newSold]);
                    }
                }
            }

            // 4. Restore voucher quota if applied
            if ($lockedOrder->voucher_code) {
                $voucher = Voucher::where('code', $lockedOrder->voucher_code)->first();
                if ($voucher) {
                    $voucher->increment('quota');
                }
            }

            // 5. Update order status to CANCELLED via FSM
            $lockedOrder->transitionTo(Order::STATUS_CANCELLED);

            // 6. Notifications
            AppNotification::send(
                $lockedOrder->user_id,
                'Pesanan Dibatalkan & Dana Dikembalikan',
                "Pesanan #{$lockedOrder->invoice_number} telah dibatalkan ({$reason})." . ($wasPaidOrProcessing ? " Dana senilai Rp " . number_format($lockedOrder->total_amount, 0, ',', '.') . " telah dikembalikan ke saldo NitipPay Anda." : " Stok barang telah dipulihkan."),
                'order',
                route('customer.dashboard')
            );

            if ($lockedOrder->store && $lockedOrder->store->user_id) {
                AppNotification::send(
                    $lockedOrder->store->user_id,
                    'Pesanan Dibatalkan',
                    "Pesanan #{$lockedOrder->invoice_number} telah dibatalkan ({$reason}). Stok produk otomatis dipulihkan.",
                    'order',
                    route('seller.orders.index')
                );
            }

            // 7. Cancel on Midtrans if payment reference exists
            if (!empty($lockedOrder->payment_reference)) {
                try {
                    $serverKey = config('services.midtrans.server_key');
                    if ($serverKey) {
                        Http::withBasicAuth($serverKey, '')
                            ->timeout(5)
                            ->post("https://api.sandbox.midtrans.com/v2/{$lockedOrder->payment_reference}/cancel");
                    }
                } catch (\Throwable $e) {
                    Log::warning("Midtrans cancel request skipped: " . $e->getMessage());
                }
            }

            return true;
        });
    }
}
