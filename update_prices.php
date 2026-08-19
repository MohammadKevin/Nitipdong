<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATE HARGA PRODUK ===\n\n";

// Contoh update harga
$updates = [
    // Format: 'ID' => ['price' => harga_baru, 'discount' => diskon_baru]
    27 => ['price' => 18999000, 'discount' => 5],    // iPhone 15 Pro Max
    28 => ['price' => 16499000, 'discount' => 8],    // Samsung S24 Ultra
    29 => ['price' => 2824000, 'discount' => 15],     // Mouse Pulsar X2
];

foreach ($updates as $productId => $data) {
    $product = \App\Models\Product::find($productId);

    if ($product) {
        $oldPrice = $product->price;
        $oldDiscount = $product->discount_percentage;

        $product->update([
            'price' => $data['price'],
            'discount_percentage' => $data['discount'],
        ]);

        echo "✅ ID {$productId}: {$product->name}\n";
        echo "   Harga lama: Rp " . number_format($oldPrice, 0, ',', '.') . " (Diskon {$oldDiscount}%)\n";
        echo "   Harga baru: Rp " . number_format($data['price'], 0, ',', '.') . " (Diskon {$data['discount']}%)\n";
        echo "   Harga customer: Rp " . number_format($product->final_price, 0, ',', '.') . "\n\n";
    } else {
        echo "❌ Produk ID {$productId} tidak ditemukan\n\n";
    }
}

echo "CATATAN:\n";
echo "- 'price' adalah harga DASAR SELLER (sebelum markup platform 5%)\n";
echo "- Harga yang dilihat customer = price * 1.05 * (1 - discount/100)\n";
echo "- Untuk ubah harga, edit array \$updates di file ini\n";
