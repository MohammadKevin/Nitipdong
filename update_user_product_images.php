<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

echo "=== MENGHUBUNGKAN FOTO DI PUBLIC/IMG KE PRODUK YANG SESUAI ===\n\n";

$mappings = [
    // 1. YIQII Lemari -> Lemari-yiqi.jpeg
    'yiqii-50cm-60cm-lemari-pakaian-plastik-transparan' => 'img/Lemari-yiqi.jpeg',
    'lemari-plastik-5-susun' => 'img/Lemari-yiqi.jpeg',

    // 2. Philips Air Fryer -> philips-air-fryer.jpg
    'philips-air-fryer-hd9200-4-1l' => 'img/philips-air-fryer.jpg',
    'blender-2l-7-speed' => 'img/philips-air-fryer.jpg',

    // 3. iPhone 15 Pro Max -> iphone-15-pro-max.jpg
    'iphone-15-pro-max-256gb-natural-titanium' => 'img/iphone-15-pro-max.jpg',
    'iphone-15-pro-max-256gb' => 'img/iphone-15-pro-max.jpg',

    // 4. Samsung S24 Ultra -> samsung-s24-ultra.jpg
    'samsung-galaxy-s24-ultra-512gb' => 'img/samsung-s24-ultra.jpg',

    // 5. iPad Pro -> apple-ipad.webp
    'ipad-pro-11-inch-m4-oled-256gb' => 'img/apple-ipad.webp',

    // 6. Pulsar Gaming Mouse -> pulsar-x-susanto.jpg
    'pulsar-x2-wireless-gaming-mouse-white' => 'img/pulsar-x-susanto.jpg',
    'pulsar-x2-wireless-gaming-mouse' => 'img/pulsar-x-susanto.jpg',

    // 7. Mechanical Keyboard / Gaming Keyboard -> rexus-daxa.jpg
    'razer-blackwidow-v4-pro-mechanical-keyboard' => 'img/rexus-daxa.jpg',
];

$updatedCount = 0;

foreach (Product::all() as $product) {
    // Check by slug
    if (isset($mappings[$product->slug])) {
        $product->image = $mappings[$product->slug];
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
        continue;
    }

    // Check by name keywords
    $nameLower = strtolower($product->name);
    if (str_contains($nameLower, 'yiqii') || str_contains($nameLower, 'lemari')) {
        $product->image = 'img/Lemari-yiqi.jpeg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'philips') || str_contains($nameLower, 'air fryer')) {
        $product->image = 'img/philips-air-fryer.jpg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'iphone 15')) {
        $product->image = 'img/iphone-15-pro-max.jpg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'samsung') || str_contains($nameLower, 's24')) {
        $product->image = 'img/samsung-s24-ultra.jpg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'ipad')) {
        $product->image = 'img/apple-ipad.webp';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'pulsar') || (str_contains($nameLower, 'mouse') && !str_contains($nameLower, 'logitech'))) {
        $product->image = 'img/pulsar-x-susanto.jpg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    } elseif (str_contains($nameLower, 'keyboard') || str_contains($nameLower, 'rexus')) {
        $product->image = 'img/rexus-daxa.jpg';
        $product->save();
        echo "✅ [ID {$product->id}] {$product->name} -> {$product->image}\n";
        $updatedCount++;
    }
}

echo "\nTotal {$updatedCount} produk diperbarui fotonya sesuai aset lokal public/img!\n";
