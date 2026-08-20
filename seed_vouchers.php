<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Voucher;
use App\Models\Store;

// Clear existing vouchers
Voucher::query()->delete();

$store = Store::first();

// Create multiple vouchers like Shopee
$vouchers = [
    [
        'code' => 'HALLSTAR',
        'name' => 'Hall & Star - SEMUA KATEGORI',
        'description' => 'SPayLater Cicilan s.d. 24 Bln',
        'type' => 'percent',
        'amount' => 10,
        'min_spend' => 0,
        'max_discount' => 50000,
        'quota' => 1000,
        'is_active' => true,
        'expires_at' => now()->addMonths(2),
    ],
    [
        'code' => 'SHOPEESTAR',
        'name' => 'ShopeeSTAR - SEMUA KATEGORI',
        'description' => 'Segera habis • s.d. 23.08.2026',
        'type' => 'percent',
        'amount' => 15,
        'min_spend' => 0,
        'max_discount' => 100000,
        'quota' => 500,
        'is_active' => true,
        'expires_at' => now()->addDays(30),
    ],
    [
        'code' => 'VIP20',
        'name' => 'Diskon 20% s.d. Rp30RB',
        'description' => 'Hingga 07.09.2026',
        'type' => 'percent',
        'amount' => 20,
        'min_spend' => 50000,
        'max_discount' => 30000,
        'quota' => 200,
        'is_active' => true,
        'expires_at' => now()->addMonths(1),
    ],
    [
        'code' => 'DISKON10',
        'name' => 'Diskon 10% s.d. Rp50RB',
        'description' => 'sisa: 5 jam',
        'type' => 'percent',
        'amount' => 10,
        'min_spend' => 50000,
        'max_discount' => 50000,
        'quota' => 100,
        'is_active' => true,
        'expires_at' => now()->addHours(5),
    ],
    [
        'code' => 'DISKON8',
        'name' => 'Diskon 8% s.d. Rp500RB',
        'description' => 'Hingga 23.08.2026',
        'type' => 'percent',
        'amount' => 8,
        'min_spend' => 50000,
        'max_discount' => 500000,
        'quota' => 150,
        'is_active' => true,
        'expires_at' => now()->addMonths(1),
    ],
    [
        'code' => 'DISKON99',
        'name' => 'Diskon 99% s.d. Rp10RB',
        'description' => 'Segera habis • s.d. 31.08.2026',
        'type' => 'percent',
        'amount' => 99,
        'min_spend' => 0,
        'max_discount' => 10000,
        'quota' => 50,
        'is_active' => true,
        'expires_at' => now()->addMonth(),
    ],
    [
        'code' => 'GRATIS37K',
        'name' => 'Gratis Ongkir Xtra',
        'description' => 'diskon -Rp37,88RB',
        'type' => 'fixed',
        'amount' => 37880,
        'min_spend' => 0,
        'max_discount' => 37880,
        'quota' => 300,
        'is_active' => true,
        'expires_at' => now()->addDays(7),
    ],
];

foreach ($vouchers as $voucherData) {
    if ($store) {
        $voucherData['store_id'] = $store->id;
    }

    Voucher::create($voucherData);
}

echo "\n✅ " . count($vouchers) . " voucher berhasil ditambahkan!\n\n";
echo "Voucher codes:\n";
foreach ($vouchers as $v) {
    echo "  - {$v['code']}: {$v['name']}\n";
}
echo "\n";
