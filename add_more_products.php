<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Store;
use App\Models\Category;
use App\Models\Product;

$store = Store::first();

// Get all categories
$categories = Category::all()->keyBy('slug');

// Clear existing products first
Product::query()->delete();

// Array of products to add
$products = [
    // Elektronik & Handphone
    [
        'category' => 'handphone-tablet',
        'name' => 'iPhone 15 Pro Max 256GB - Natural Titanium',
        'slug' => 'iphone-15-pro-max-256gb',
        'description' => 'iPhone 15 Pro Max dengan chip A17 Pro, kamera 48MP, dan layar Super Retina XDR 6.7 inci.',
        'price' => 19000000,
        'discount_percentage' => 5,
        'stock' => 50,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'handphone-tablet',
        'name' => 'Samsung Galaxy S24 Ultra 512GB - Titanium Black',
        'slug' => 'samsung-galaxy-s24-ultra-512gb',
        'description' => 'Samsung S24 Ultra dengan S Pen, kamera 200MP, dan layar Dynamic AMOLED 6.8 inci.',
        'price' => 18000000,
        'discount_percentage' => 8,
        'stock' => 30,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],
    [
        'category' => 'handphone-tablet',
        'name' => 'Xiaomi 14 Ultra 16GB/512GB - Black',
        'slug' => 'xiaomi-14-ultra',
        'description' => 'Flagship Xiaomi dengan kamera Leica, Snapdragon 8 Gen 3, dan fast charging 90W.',
        'price' => 12500000,
        'discount_percentage' => 10,
        'stock' => 40,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],

    // Gaming
    [
        'category' => 'gaming',
        'name' => 'Pulsar X2 Wireless Gaming Mouse - Medium',
        'slug' => 'pulsar-x2-wireless-gaming-mouse',
        'description' => 'Gaming mouse wireless ultra ringan 59g dengan sensor PAW3395, polling rate 1000Hz.',
        'price' => 950000,
        'discount_percentage' => 15,
        'stock' => 100,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],
    [
        'category' => 'gaming',
        'name' => 'Logitech G Pro X Superlight Wireless - White',
        'slug' => 'logitech-g-pro-x-superlight',
        'description' => 'Gaming mouse profesional dengan sensor HERO 25K dan bobot 63g.',
        'price' => 1850000,
        'discount_percentage' => 12,
        'stock' => 60,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],
    [
        'category' => 'gaming',
        'name' => 'PlayStation 5 Slim Digital Edition',
        'slug' => 'ps5-slim-digital',
        'description' => 'Konsol game next-gen dengan SSD ultra cepat dan grafis 4K 120fps.',
        'price' => 6500000,
        'discount_percentage' => 5,
        'stock' => 25,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],

    // Komputer & Laptop
    [
        'category' => 'komputer-laptop',
        'name' => 'MacBook Pro 14" M3 Pro 18GB/512GB - Space Black',
        'slug' => 'macbook-pro-14-m3-pro',
        'description' => 'MacBook Pro dengan chip M3 Pro untuk performa profesional.',
        'price' => 32000000,
        'discount_percentage' => 3,
        'stock' => 15,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'komputer-laptop',
        'name' => 'ASUS ROG Zephyrus G14 2024 - RTX 4060',
        'slug' => 'asus-rog-g14-2024',
        'description' => 'Gaming laptop compact dengan Ryzen 9 dan RTX 4060.',
        'price' => 24000000,
        'discount_percentage' => 10,
        'stock' => 20,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],

    // Fashion Pria
    [
        'category' => 'fashion-pria',
        'name' => 'Kemeja Pria Lengan Panjang Slim Fit - Navy',
        'slug' => 'kemeja-pria-navy',
        'description' => 'Kemeja formal premium dengan bahan katun breathable.',
        'price' => 250000,
        'discount_percentage' => 20,
        'stock' => 150,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'fashion-pria',
        'name' => 'Celana Jeans Pria Regular Fit - Dark Blue',
        'slug' => 'celana-jeans-pria',
        'description' => 'Jeans premium dengan bahan denim berkualitas tinggi.',
        'price' => 350000,
        'discount_percentage' => 25,
        'stock' => 200,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'fashion-pria',
        'name' => 'Sepatu Sneakers Pria Casual - White/Black',
        'slug' => 'sepatu-sneakers-pria',
        'description' => 'Sneakers stylish untuk aktivitas casual sehari-hari.',
        'price' => 450000,
        'discount_percentage' => 30,
        'stock' => 80,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],

    // Fashion Wanita
    [
        'category' => 'fashion-wanita',
        'name' => 'Dress Wanita Casual A-Line - Floral',
        'slug' => 'dress-wanita-floral',
        'description' => 'Dress cantik dengan motif floral untuk acara santai.',
        'price' => 280000,
        'discount_percentage' => 35,
        'stock' => 120,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],
    [
        'category' => 'fashion-wanita',
        'name' => 'Tas Selempang Wanita - Leather Brown',
        'slug' => 'tas-selempang-wanita',
        'description' => 'Tas kulit sintetis premium dengan desain minimalis.',
        'price' => 320000,
        'discount_percentage' => 40,
        'stock' => 90,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],

    // Kesehatan
    [
        'category' => 'kesehatan',
        'name' => 'Vitamin C 1000mg Isi 60 Tablet',
        'slug' => 'vitamin-c-1000mg',
        'description' => 'Suplemen vitamin C untuk menjaga daya tahan tubuh.',
        'price' => 85000,
        'discount_percentage' => 15,
        'stock' => 300,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'kesehatan',
        'name' => 'Masker Medis 3 Ply Isi 50pcs - Blue',
        'slug' => 'masker-medis-3-ply',
        'description' => 'Masker medis standar kesehatan dengan filter 3 lapis.',
        'price' => 45000,
        'discount_percentage' => 20,
        'stock' => 500,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],

    // Kecantikan
    [
        'category' => 'kecantikan',
        'name' => 'Serum Wajah Vitamin C & E - 30ml',
        'slug' => 'serum-wajah-vitamin-ce',
        'description' => 'Serum pencerah wajah dengan kandungan vitamin C dan E.',
        'price' => 150000,
        'discount_percentage' => 25,
        'stock' => 180,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],
    [
        'category' => 'kecantikan',
        'name' => 'Lipstick Matte Long Lasting - Red Berry',
        'slug' => 'lipstick-matte-red',
        'description' => 'Lipstick matte dengan ketahanan hingga 12 jam.',
        'price' => 95000,
        'discount_percentage' => 30,
        'stock' => 250,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],

    // Olahraga
    [
        'category' => 'olahraga',
        'name' => 'Sepatu Lari Nike Air Zoom Pegasus - Black',
        'slug' => 'sepatu-lari-nike-pegasus',
        'description' => 'Sepatu running dengan teknologi Air Zoom untuk performa maksimal.',
        'price' => 1850000,
        'discount_percentage' => 18,
        'stock' => 70,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],
    [
        'category' => 'olahraga',
        'name' => 'Matras Yoga Premium 6mm - Purple',
        'slug' => 'matras-yoga-premium',
        'description' => 'Matras yoga anti-slip dengan ketebalan 6mm.',
        'price' => 180000,
        'discount_percentage' => 22,
        'stock' => 100,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],
    [
        'category' => 'olahraga',
        'name' => 'Dumbbell Set 20kg - Black Chrome',
        'slug' => 'dumbbell-set-20kg',
        'description' => 'Set dumbbell adjustable 20kg untuk home workout.',
        'price' => 650000,
        'discount_percentage' => 15,
        'stock' => 45,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],

    // Rumah Tangga
    [
        'category' => 'rumah-tangga',
        'name' => 'Lemari Plastik 5 Susun - White/Brown',
        'slug' => 'lemari-plastik-5-susun',
        'description' => 'Lemari serbaguna dengan 5 tingkat, tidak perlu instalasi.',
        'price' => 385000,
        'discount_percentage' => 35,
        'stock' => 60,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],
    [
        'category' => 'rumah-tangga',
        'name' => 'Blender 2L 7 Speed - Red',
        'slug' => 'blender-2l-7-speed',
        'description' => 'Blender multifungsi dengan 7 kecepatan dan pisau tajam.',
        'price' => 420000,
        'discount_percentage' => 28,
        'stock' => 85,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'rumah-tangga',
        'name' => 'Set Panci Anti Lengket 5pcs - Grey',
        'slug' => 'set-panci-anti-lengket',
        'description' => 'Set panci dengan lapisan anti lengket dan tutup kaca.',
        'price' => 550000,
        'discount_percentage' => 32,
        'stock' => 70,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],

    // Makanan & Minuman
    [
        'category' => 'makanan-minuman',
        'name' => 'Kopi Arabica Premium 200gr - Robusta Blend',
        'slug' => 'kopi-arabica-premium',
        'description' => 'Kopi arabica pilihan dengan cita rasa smooth dan aromatic.',
        'price' => 125000,
        'discount_percentage' => 20,
        'stock' => 200,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'makanan-minuman',
        'name' => 'Madu Murni 500ml - Multiflora',
        'slug' => 'madu-murni-500ml',
        'description' => 'Madu asli 100% tanpa campuran gula atau pemanis.',
        'price' => 95000,
        'discount_percentage' => 15,
        'stock' => 150,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],

    // Buku & Alat Tulis
    [
        'category' => 'buku-alat-tulis',
        'name' => 'Buku Catatan A5 Hardcover - Dotted',
        'slug' => 'buku-catatan-a5-dotted',
        'description' => 'Notebook premium dengan halaman dotted dan sampul hardcover.',
        'price' => 65000,
        'discount_percentage' => 25,
        'stock' => 300,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],
    [
        'category' => 'buku-alat-tulis',
        'name' => 'Pen Gel 0.5mm Set 12 Warna',
        'slug' => 'pen-gel-12-warna',
        'description' => 'Set pulpen gel dengan tinta smooth dan warna-warni cerah.',
        'price' => 48000,
        'discount_percentage' => 30,
        'stock' => 400,
        'image' => 'img/pulsar-x-susanto.jpg',
    ],

    // Otomotif
    [
        'category' => 'otomotif',
        'name' => 'Ban Motor Tubeless 80/90-17 - Corsa',
        'slug' => 'ban-motor-tubeless',
        'description' => 'Ban motor tubeless dengan grip maksimal untuk segala cuaca.',
        'price' => 275000,
        'discount_percentage' => 18,
        'stock' => 120,
        'image' => 'img/iphone-15-pro-max.jpg',
    ],
    [
        'category' => 'otomotif',
        'name' => 'Oli Mesin Mobil Full Synthetic 4L - Shell',
        'slug' => 'oli-mesin-full-synthetic',
        'description' => 'Oli mesin full synthetic untuk performa mesin optimal.',
        'price' => 420000,
        'discount_percentage' => 12,
        'stock' => 80,
        'image' => 'img/samsung-s24-ultra.jpg',
    ],
];

$count = 0;
foreach ($products as $productData) {
    $categorySlug = $productData['category'];
    unset($productData['category']);

    if (isset($categories[$categorySlug])) {
        $productData['store_id'] = $store->id;
        $productData['category_id'] = $categories[$categorySlug]->id;
        $productData['is_active'] = true;

        Product::create($productData);
        $count++;
    }
}

echo "\n✅ {$count} produk berhasil ditambahkan!\n\n";
echo "Produk dari berbagai kategori:\n";
echo "  - Handphone & Tablet: 3 produk\n";
echo "  - Gaming: 3 produk\n";
echo "  - Komputer & Laptop: 2 produk\n";
echo "  - Fashion Pria: 3 produk\n";
echo "  - Fashion Wanita: 2 produk\n";
echo "  - Kesehatan: 2 produk\n";
echo "  - Kecantikan: 2 produk\n";
echo "  - Olahraga: 3 produk\n";
echo "  - Rumah Tangga: 3 produk\n";
echo "  - Makanan & Minuman: 2 produk\n";
echo "  - Buku & Alat Tulis: 2 produk\n";
echo "  - Otomotif: 2 produk\n\n";
