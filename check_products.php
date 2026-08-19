<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRODUK AKTIF ===\n\n";

$products = \App\Models\Product::with('category')->get();

foreach ($products as $p) {
    echo "ID {$p->id}: {$p->name}\n";
    echo "  Kategori: " . ($p->category->name ?? '-') . "\n";
    echo "  Harga: Rp " . number_format($p->price, 0, ',', '.') . "\n";
    echo "  Diskon: {$p->discount_percentage}%\n";
    echo "  Stok: {$p->stock} unit\n";
    echo "  Foto: {$p->image}\n";
    echo "  File exists: " . (file_exists(public_path($p->image)) ? 'YES ✅' : 'NO ❌') . "\n\n";
}

echo "Total: {$products->count()} produk\n";
