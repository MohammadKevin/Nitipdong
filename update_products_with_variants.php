<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

// Update iPhone 15 Pro Max
$iphone = Product::where('slug', 'iphone-15-pro-max-256gb')->first();
if ($iphone) {
    $iphone->update([
        'specifications' => [
            'Kondisi' => 'Baru',
            'Berat Satuan' => '1,8 - 8,5 kg',
            'Min. Pembelian' => '1 Buah',
            'Kategori' => 'Handphone & Tablet',
            'Etalase' => 'Semua Etalase',
        ],
        'variants' => [
            [
                'name' => 'Warna',
                'options' => ['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium']
            ],
            [
                'name' => 'Storage',
                'options' => ['256GB', '512GB', '1TB']
            ],
            [
                'name' => 'RAM',
                'options' => ['8GB']
            ]
        ],
        'weight' => 2.5,
        'condition' => 'new'
    ]);
    echo "✅ Updated: iPhone 15 Pro Max with variants\n";
}

// Update Samsung S24 Ultra
$samsung = Product::where('slug', 'samsung-galaxy-s24-ultra-512gb')->first();
if ($samsung) {
    $samsung->update([
        'specifications' => [
            'Kondisi' => 'Baru',
            'Berat Satuan' => '2 kg',
            'Min. Pembelian' => '1 Buah',
            'Kategori' => 'Handphone & Tablet',
            'Etalase' => 'Semua Etalase',
        ],
        'variants' => [
            [
                'name' => 'Warna',
                'options' => ['Titanium Black', 'Titanium Gray', 'Titanium Violet', 'Titanium Yellow']
            ],
            [
                'name' => 'Storage',
                'options' => ['256GB', '512GB', '1TB']
            ],
            [
                'name' => 'RAM',
                'options' => ['12GB']
            ]
        ],
        'weight' => 2.0,
        'condition' => 'new'
    ]);
    echo "✅ Updated: Samsung S24 Ultra with variants\n";
}

// Update Lemari Plastik
$lemari = Product::where('slug', 'lemari-plastik-5-susun')->first();
if ($lemari) {
    $lemari->update([
        'specifications' => [
            'Kondisi' => 'Baru',
            'Berat Satuan' => '1,8 - 8,5 kg',
            'Min. Pembelian' => '1 Buah',
            'Kategori' => 'Rumah Tangga',
            'Etalase' => 'Furniture',
            'Features' => [
                'Free Installation & Easy To Install',
                'Stable & Strong Bearing',
                'Smooth Handle Design',
                'Stackable & Foldable',
                'High Quality Material'
            ]
        ],
        'variants' => [
            [
                'name' => 'Ukuran',
                'options' => ['50CM-4 lantai', '50CM-6 lantai', '60CM-6 lantai', '70CM-5 lantai', '60CM-2 lantai-Putih', '60CM-3 lantai-Putih', '60CM-4 lantai-Putih', '60CM-5 lantai-Putih', '70CM-2 lantai-Putih', '70CM-3 lantai-Putih', '70CM-4 lantai-Putih', '70CM-5 lantai-Putih', '70CM-6 lantai-Putih']
            ],
            [
                'name' => 'Warna',
                'options' => ['White/Brown', 'Putih']
            ]
        ],
        'weight' => 5.0,
        'condition' => 'new'
    ]);
    echo "✅ Updated: Lemari Plastik 5 Susun with variants\n";
}

// Update Mouse Gaming
$mouse = Product::where('slug', 'pulsar-x2-wireless-gaming-mouse')->first();
if ($mouse) {
    $mouse->update([
        'specifications' => [
            'Kondisi' => 'Baru',
            'Berat' => '59 gram',
            'Min. Pembelian' => '1 Buah',
            'Kategori' => 'Gaming',
            'Etalase' => 'Gaming Gear',
        ],
        'variants' => [
            [
                'name' => 'Warna',
                'options' => ['Black', 'White', 'Pink']
            ],
            [
                'name' => 'Size',
                'options' => ['Small', 'Medium', 'Large']
            ]
        ],
        'weight' => 0.15,
        'condition' => 'new'
    ]);
    echo "✅ Updated: Pulsar X2 Mouse with variants\n";
}

// Update MacBook Pro
$macbook = Product::where('slug', 'macbook-pro-14-m3-pro')->first();
if ($macbook) {
    $macbook->update([
        'specifications' => [
            'Kondisi' => 'Baru',
            'Berat Satuan' => '3 kg',
            'Min. Pembelian' => '1 Buah',
            'Kategori' => 'Komputer & Laptop',
            'Etalase' => 'Apple Products',
        ],
        'variants' => [
            [
                'name' => 'Warna',
                'options' => ['Space Black', 'Silver']
            ],
            [
                'name' => 'RAM',
                'options' => ['18GB', '36GB', '64GB']
            ],
            [
                'name' => 'Storage',
                'options' => ['512GB', '1TB', '2TB', '4TB']
            ]
        ],
        'weight' => 3.0,
        'condition' => 'new'
    ]);
    echo "✅ Updated: MacBook Pro with variants\n";
}

echo "\n✅ All products updated with variants and specifications!\n\n";
