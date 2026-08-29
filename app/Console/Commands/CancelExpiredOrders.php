<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan pending yang sudah melewati batas waktu pembayaran 24 jam dan pulihkan stok barang serta kuota voucher.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa pesanan yang kedaluwarsa...');

        $expiredOrders = Order::where('status', 'pending')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('expires_at')
                      ->where('expires_at', '<', now());
                })->orWhere(function ($q) {
                    $q->whereNull('expires_at')
                      ->where('created_at', '<', now()->subHours(24));
                });
            })
            ->with(['orderItems.product', 'store'])
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada pesanan kedaluwarsa yang ditemukan.');
            return 0;
        }

        $cancelledCount = 0;

        foreach ($expiredOrders as $order) {
            try {
                DB::transaction(function () use ($order) {
                    $order->update([
                        'status' => 'cancelled',
                    ]);

                    // Pulihkan stok produk
                    foreach ($order->orderItems as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                            $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                        }
                    }

                    // Pulihkan kuota voucher jika ada
                    if ($order->voucher_code) {
                        $voucher = Voucher::where('code', $order->voucher_code)->first();
                        if ($voucher) {
                            $voucher->increment('quota');
                        }
                    }

                    // Notifikasi ke pembeli
                    AppNotification::send(
                        $order->user_id,
                        'Pesanan Kedaluwarsa & Dibatalkan',
                        "Pesanan #{$order->invoice_number} telah dibatalkan otomatis oleh sistem karena melewati batas waktu pembayaran 24 jam.",
                        'order',
                        route('customer.dashboard')
                    );

                    // Notifikasi ke penjual
                    if ($order->store && $order->store->user_id) {
                        AppNotification::send(
                            $order->store->user_id,
                            'Pesanan Kedaluwarsa Dibatalkan',
                            "Pesanan #{$order->invoice_number} dibatalkan otomatis karena pembeli tidak menyelesaikan pembayaran dalam 24 jam. Stok produk telah dipulihkan.",
                            'order',
                            route('seller.orders.index')
                        );
                    }
                });

                // Batalkan di Midtrans jika ada reference transaksi
                if (!empty($order->payment_reference)) {
                    try {
                        $serverKey = config('services.midtrans.server_key');
                        if ($serverKey) {
                            Http::withBasicAuth($serverKey, '')
                                ->timeout(5)
                                ->post("https://api.sandbox.midtrans.com/v2/{$order->payment_reference}/cancel");
                        }
                    } catch (\Exception $e) {
                        Log::warning("Gagal membatalkan transaksi Midtrans #{$order->payment_reference}: " . $e->getMessage());
                    }
                }

                $cancelledCount++;
                $this->line("<comment>Pesanan #{$order->invoice_number} berhasil dibatalkan.</comment>");
            } catch (\Exception $e) {
                Log::error("Error membatalkan pesanan #{$order->id}: " . $e->getMessage());
                $this->error("Gagal membatalkan pesanan #{$order->invoice_number}: " . $e->getMessage());
            }
        }

        $this->info("Selesai. Total {$cancelledCount} pesanan kedaluwarsa berhasil dibatalkan dan stok telah dipulihkan.");

        return 0;
    }
}
