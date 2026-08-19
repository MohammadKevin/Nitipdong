<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "=== HAPUS SEMUA KEYBOARD ===\n\n";

// Cari semua produk keyboard
$keyboards = Product::where('name', 'like', '%keyboard%')
    ->orWhere('name', 'like', '%rexus%')
    ->get();

if ($keyboards->isEmpty()) {
    echo "❌ Tidak ada produk keyboard yang ditemukan.\n";
    exit;
}

echo "Produk keyboard yang akan dihapus:\n";
foreach ($keyboards as $keyboard) {
    echo "  ID: {$keyboard->id}\n";
    echo "  Nama: {$keyboard->name}\n";
    echo "  Harga: Rp " . number_format($keyboard->price * 1.05, 0, ',', '.') . "\n\n";
}

// Hapus semua keyboard
$count = $keyboards->count();
Product::where('name', 'like', '%keyboard%')
    ->orWhere('name', 'like', '%rexus%')
    ->delete();

echo "✅ {$count} produk keyboard berhasil dihapus!\n\n";

$remaining = Product::count();
echo "Total produk sekarang: {$remaining}\n\n";

echo "=== PRODUK TERSISA ===\n";
$products = Product::all();
foreach ($products as $product) {
    echo "ID {$product->id}: {$product->name}\n";
    echo "  Harga: Rp " . number_format($product->price * 1.05, 0, ',', '.') . "\n";
    echo "  Foto: {$product->image}\n\n";
}
