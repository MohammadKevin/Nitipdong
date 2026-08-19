<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "=== TEST ASSET URL ===\n\n";

$product = Product::find(27); // iPhone

echo "Product: {$product->name}\n";
echo "Image DB: {$product->image}\n";
echo "Image URL Accessor: {$product->image_url}\n";
echo "Asset Helper: " . asset($product->image) . "\n";
echo "APP_URL: " . config('app.url') . "\n\n";

// Test file path
$publicPath = public_path($product->image);
echo "Public Path: {$publicPath}\n";
echo "File Exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
