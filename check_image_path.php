<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$iphone = \App\Models\Product::find(27);
$samsung = \App\Models\Product::find(28);

echo "=== CEK PATH FOTO ===\n\n";
echo "iPhone 15 Pro Max:\n";
echo "  DB image field: " . $iphone->image . "\n";
echo "  URL yang diharapkan: " . asset($iphone->image) . "\n";
echo "  File exists? " . (file_exists(public_path($iphone->image)) ? 'YES' : 'NO') . "\n\n";

echo "Samsung S24 Ultra:\n";
echo "  DB image field: " . $samsung->image . "\n";
echo "  URL yang diharapkan: " . asset($samsung->image) . "\n";
echo "  File exists? " . (file_exists(public_path($samsung->image)) ? 'YES' : 'NO') . "\n\n";

echo "View menggunakan: asset('storage/' . \$product->image)\n";
echo "Yang akan menjadi: " . asset('storage/' . $iphone->image) . "\n";
echo "File di path itu ada? " . (file_exists(public_path('storage/' . $iphone->image)) ? 'YES' : 'NO') . "\n";
