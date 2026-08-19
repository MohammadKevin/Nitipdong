<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "=== CEK GAMBAR PRODUK ===\n\n";

$products = Product::all();

foreach ($products as $p) {
    echo "ID {$p->id}: {$p->name}\n";
    echo "  Image DB: {$p->image}\n";
    echo "  Image URL: {$p->image_url}\n";

    // Check file exists
    if ($p->image && str_starts_with($p->image, 'img/')) {
        $filePath = public_path($p->image);
        $exists = file_exists($filePath) ? 'YES ✓' : 'NO ✗';
        echo "  File exists: {$exists} ({$filePath})\n";
    }
    echo "\n";
}
