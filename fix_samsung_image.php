<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

// Fix Samsung
$samsung = Product::where('name', 'like', '%Samsung%')->first();
if ($samsung) {
    $samsung->image = 'img/samsung-s24-ultra.jpg';
    $samsung->save();
    echo "✅ Samsung S24 updated: img/samsung-s24-ultra.jpg\n";
}

// Fix Pulsar Mouse
$pulsar = Product::where('name', 'like', '%Pulsar%')->first();
if ($pulsar) {
    $pulsar->image = 'img/pulsar-x-susanto.jpg';
    $pulsar->save();
    echo "✅ Pulsar X2 updated: img/pulsar-x-susanto.jpg\n";
}

// Fix iPhone
$iphone = Product::where('name', 'like', '%iPhone%')->first();
if ($iphone) {
    $iphone->image = 'img/iphone-15-pro-max.jpg';
    $iphone->save();
    echo "✅ iPhone 15 Pro Max updated: img/iphone-15-pro-max.jpg\n";
}

// Fix Razer
$razer = Product::where('name', 'like', '%Razer%')->orWhere('name', 'like', '%keyboard%')->first();
if ($razer) {
    $razer->image = 'img/razer.jpg';
    $razer->save();
    echo "✅ Razer keyboard updated: img/razer.jpg\n";
}

// Fix lipstick
$lipstik = Product::where('name', 'like', '%Lipstick%')->first();
if ($lipstik) {
    $lipstik->image = 'img/lipstik.jpg';
    $lipstik->save();
    echo "✅ Lipstick updated: img/lipstik.jpg\n";
}

// Fix serum
$serum = Product::where('name', 'like', '%Serum%')->first();
if ($serum) {
    $serum->image = 'img/ordinary.jpg';
    $serum->save();
    echo "✅ Serum updated: img/ordinary.jpg\n";
}

echo "\n✅ Specific product images fixed!\n";
