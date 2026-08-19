<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATE FOTO PRODUK SMARTPHONE ===\n\n";

// Update iPhone 15 Pro Max
$iphone = \App\Models\Product::find(27);
if ($iphone) {
    $iphone->update([
        'image' => 'img/iphone-15-pro-max.jpg'
    ]);
    echo "✅ iPhone 15 Pro Max - Foto updated: img/iphone-15-pro-max.jpg\n";
} else {
    echo "❌ iPhone 15 Pro Max tidak ditemukan (ID: 27)\n";
}

// Update Samsung S24 Ultra
$samsung = \App\Models\Product::find(28);
if ($samsung) {
    $samsung->update([
        'image' => 'img/samsung-s24-ultra.jpg'
    ]);
    echo "✅ Samsung S24 Ultra - Foto updated: img/samsung-s24-ultra.jpg\n";
} else {
    echo "❌ Samsung S24 Ultra tidak ditemukan (ID: 28)\n";
}

echo "\n🎉 Update selesai!\n";
echo "\nCATATAN: Foto disimpan di public/img/\n";
echo "  - public/img/iphone-15-pro-max.jpg\n";
echo "  - public/img/samsung-s24-ultra.jpg\n";
echo "\nSilakan ganti file placeholder dengan foto asli!\n";
