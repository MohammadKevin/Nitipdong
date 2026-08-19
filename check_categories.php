<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== KATEGORI & ICON ===\n\n";

$categories = \App\Models\Category::orderBy('name')->get();

foreach ($categories as $cat) {
    $productCount = \App\Models\Product::where('category_id', $cat->id)->count();

    echo "📂 {$cat->name}\n";
    echo "   Slug: {$cat->slug}\n";
    echo "   Icon: {$cat->icon}\n";
    echo "   Produk: {$productCount}\n\n";
}

echo "Total: {$categories->count()} kategori\n";
