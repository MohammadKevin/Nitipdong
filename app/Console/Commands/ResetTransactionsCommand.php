<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-transactions {--force : Jalankan tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh riwayat transaksi, pesanan, ulasan, keranjang, dan pulihkan stok/saldo ke awal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin mereset seluruh data transaksi & pesanan?')) {
            $this->info('Pembersihan dibatalkan.');
            return 0;
        }

        $this->info('⏳ Memulai reset data transaksi & pesanan...');

        Schema::disableForeignKeyConstraints();

        // 1. Truncate tabel riwayat transaksi
        if (Schema::hasTable('order_items')) DB::table('order_items')->truncate();
        if (Schema::hasTable('orders')) DB::table('orders')->truncate();
        if (Schema::hasTable('reviews')) DB::table('reviews')->truncate();
        if (Schema::hasTable('complaints')) DB::table('complaints')->truncate();
        if (Schema::hasTable('withdrawals')) DB::table('withdrawals')->truncate();
        if (Schema::hasTable('carts')) DB::table('carts')->truncate();
        if (Schema::hasTable('wishlists')) DB::table('wishlists')->truncate();
        if (Schema::hasTable('app_notifications')) DB::table('app_notifications')->truncate();

        // 2. Reset Saldo Toko ke Rp 0
        if (Schema::hasTable('stores')) {
            DB::table('stores')->update([
                'balance' => 0,
            ]);
        }

        // 3. Reset Penjualan Produk & Pulihkan Stok ke 50 Unit
        if (Schema::hasTable('products')) {
            DB::table('products')->update([
                'sold_count' => 0,
                'rating'     => 0.0,
                'stock'      => 50,
            ]);
        }

        Schema::enableForeignKeyConstraints();

        $this->newLine();
        $this->info('===========================================================');
        $this->info('🎉 DATABASE TRANSAKSI BERHASIL DI-RESET BERSIH!');
        $this->info('===========================================================');
        $this->line('  • Seluruh pesanan (#INV-...) & rincian item telah dihapus.');
        $this->line('  • Seluruh ulasan bintang & komplain telah dibersihkan.');
        $this->line('  • Keranjang & wishlist pelanggan telah dikosongkan.');
        $this->line('  • Stok produk dipulihkan kembali ke 50 unit (0 terjual).');
        $this->line('  • Saldo dompet semua toko resmi kembali ke Rp 0.');
        $this->line('  • Akun User, Seller, Toko, dan Produk tetap AMAN terjaga.');
        $this->newLine();

        return 0;
    }
}
