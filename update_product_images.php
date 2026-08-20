<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$imageMap = [
    'iPhone 15 Pro Max' => 'img/iphone-15-pro-max.jpg',
    'Samsung S24 Ultra' => 'img/samsung-s24-ultra.jpg',
    'Pulsar X2 Mouse' => 'img/pulsar-x-susanto.jpg',
    'Logitech G502' => 'img/logitech.jpg',
    'Logitech' => 'img/logitech.jpg',
    'MacBook Pro' => 'img/macbookm3pro.jpg',
    'MacBook' => 'img/macbookm3pro.jpg',
    'ROG Zephyrus' => 'img/rogzephyrus.jpg',
    'ASUS ROG' => 'img/rogzephyrus.jpg',
    'Razer' => 'img/razer.jpg',
    'Rexus' => 'img/rexus-daxa.jpg',
    'iPad' => 'img/apple-ipad.jpg',
    'Apple iPad' => 'img/apple-ipad.jpg',
    'PS5' => 'img/ps5resmi.webp',
    'PlayStation' => 'img/ps5resmi.webp',
    'Monitor Xiaomi' => 'img/monitorxiaomi.webp',
    'LG' => 'img/LG.avif',
    'TV LG' => 'img/LG.avif',
    'Air Fryer' => 'img/philips-air-fryer.jpg',
    'Philips' => 'img/philips-air-fryer.jpg',
    'Set Wajan' => 'img/set-wajan.jpg',
    'Wajan' => 'img/set-wajan.jpg',
    'Lemari' => 'img/Lemari-yiqi.jpeg',
    'Nike' => 'img/nike.jpg',
    'Sepatu Nike' => 'img/nike.jpg',
    'Vantela' => 'img/vantela.jpg',
    'Sepatu Vantela' => 'img/vantela.jpg',
    'Erigo' => 'img/erigo.jpg',
    'Kemeja' => 'img/kameja.jpg',
    'Dress' => 'img/dress.jpg',
    'Tas Selempang' => 'img/tasselempang.jpg',
    'Tas' => 'img/tasselempang.jpg',
    'Vitamin' => 'img/vitamin.webp',
    'Masker' => 'img/masker.jpg',
    'Lipstik' => 'img/lipstik.jpg',
    'Skintific' => 'img/skintific.jpg',
    'The Ordinary' => 'img/ordinary.jpg',
];

$products = Product::all();
$updated = 0;

foreach ($products as $product) {
    $imagePath = null;

    // Try to find matching image based on product name
    foreach ($imageMap as $keyword => $path) {
        if (stripos($product->name, $keyword) !== false) {
            $imagePath = $path;
            break;
        }
    }

    // If no match found, try to match based on category or default
    if (!$imagePath) {
        $categoryName = $product->category->name ?? '';

        if (stripos($categoryName, 'Handphone') !== false || stripos($categoryName, 'Tablet') !== false) {
            $imagePath = 'img/iphone-15-pro-max.jpg';
        } elseif (stripos($categoryName, 'Gaming') !== false) {
            $imagePath = 'img/logitech.jpg';
        } elseif (stripos($categoryName, 'Laptop') !== false || stripos($categoryName, 'Komputer') !== false) {
            $imagePath = 'img/macbookm3pro.jpg';
        } elseif (stripos($categoryName, 'Fashion') !== false) {
            $imagePath = 'img/erigo.jpg';
        } elseif (stripos($categoryName, 'Kesehatan') !== false) {
            $imagePath = 'img/vitamin.webp';
        } elseif (stripos($categoryName, 'Kecantikan') !== false) {
            $imagePath = 'img/skintific.jpg';
        } elseif (stripos($categoryName, 'Olahraga') !== false) {
            $imagePath = 'img/nike.jpg';
        } elseif (stripos($categoryName, 'Rumah') !== false) {
            $imagePath = 'img/set-wajan.jpg';
        } else {
            $imagePath = 'img/saksershop-logo.png'; // default fallback
        }
    }

    if ($imagePath && $product->image !== $imagePath) {
        $product->image = $imagePath;
        $product->save();
        $updated++;
        echo "✅ Updated: {$product->name} -> {$imagePath}\n";
    }
}

echo "\n✅ Total {$updated} products updated with images!\n";
