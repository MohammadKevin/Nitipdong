<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== HAPUS KEYBOARD REXUS ===\n\n";

$keyboard = \App\Models\Product::find(31);

if ($keyboard) {
    echo "Produk ditemukan:\n";
    echo "  ID: {$keyboard->id}\n";
    echo "  Nama: {$keyboard->name}\n";
    echo "  Harga: Rp " . number_format($keyboard->price, 0, ',', '.') . "\n\n";

    $keyboard->delete();

    echo "✅ Keyboard Rexus Daxa M84X berhasil dihapus!\n";
} else {
    echo "❌ Produk dengan ID 31 tidak ditemukan.\n";
}

echo "\nTotal produk sekarang: " . \App\Models\Product::count() . "\n";
