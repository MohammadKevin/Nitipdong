<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== MEMPERBARUI DATA TOKO & PRODUK DENGAN FOTO SESUAI 100% ===\n\n";

// 1. Pastikan kategori lengkap
$categoryList = [
    ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'icon' => 'fa-solid fa-house'],
    ['name' => 'Handphone & Tablet', 'slug' => 'handphone-tablet', 'icon' => 'fa-solid fa-mobile-screen-button'],
    ['name' => 'Gaming', 'slug' => 'gaming', 'icon' => 'fa-solid fa-gamepad'],
    ['name' => 'Komputer & Laptop', 'slug' => 'komputer-laptop', 'icon' => 'fa-solid fa-laptop'],
    ['name' => 'Fashion Pria', 'slug' => 'fashion-pria', 'icon' => 'fa-solid fa-shirt'],
    ['name' => 'Fashion Wanita', 'slug' => 'fashion-wanita', 'icon' => 'fa-solid fa-user-tie'],
    ['name' => 'Kecantikan', 'slug' => 'kecantikan', 'icon' => 'fa-solid fa-spa'],
    ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'icon' => 'fa-solid fa-heart-pulse'],
    ['name' => 'Olahraga', 'slug' => 'olahraga', 'icon' => 'fa-solid fa-dumbbell'],
    ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman', 'icon' => 'fa-solid fa-utensils'],
    ['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis', 'icon' => 'fa-solid fa-book'],
    ['name' => 'Otomotif', 'slug' => 'otomotif', 'icon' => 'fa-solid fa-car'],
    ['name' => 'Elektronik', 'slug' => 'elektronik', 'icon' => 'fa-solid fa-bolt'],
];

foreach ($categoryList as $cData) {
    Category::updateOrCreate(['slug' => $cData['slug']], $cData);
}

$categories = Category::all()->keyBy('slug');
echo "✅ " . $categories->count() . " Kategori siap.\n";

// 2. Siapkan Toko & Kota Lokasi
$storesData = [
    ['name' => 'YIQII Official Store', 'address' => 'Kota Tangerang', 'slug' => 'yiqii-official-store'],
    ['name' => 'TechZone Authorized', 'address' => 'Jakarta Barat', 'slug' => 'techzone-authorized'],
    ['name' => 'Gadget Gallery ID', 'address' => 'Jakarta Selatan', 'slug' => 'gadget-gallery-id'],
    ['name' => 'Urban Style Apparel', 'address' => 'Kota Bandung', 'slug' => 'urban-style-apparel'],
    ['name' => 'Glow & Beauty Care', 'address' => 'Kota Surabaya', 'slug' => 'glow-beauty-care'],
    ['name' => 'Healthy Life Store', 'address' => 'Kota Semarang', 'slug' => 'healthy-life-store'],
    ['name' => 'Sport Station ID', 'address' => 'Kota Medan', 'slug' => 'sport-station-id'],
    ['name' => 'OtoSpeed Performance', 'address' => 'Kota Tangerang Selatan', 'slug' => 'otospeed-performance'],
];

$seller = User::where('role', 'seller')->first() ?: User::first();

$stores = [];
foreach ($storesData as $sData) {
    $store = Store::updateOrCreate(
        ['slug' => $sData['slug']],
        [
            'user_id' => $seller->id,
            'name' => $sData['name'],
            'address' => $sData['address'],
            'description' => 'Official verified store di BelanjaIn - ' . $sData['name'],
            'status' => 'approved',
        ]
    );
    $stores[$sData['slug']] = $store;
}

$defaultStore = $stores['yiqii-official-store'];
echo "✅ Toko & Lokasi Kota Terkonfigurasi.\n";

// 3. Clear existing products to ensure clean matching data
Product::query()->delete();

// 4. Daftar 32+ Produk Lengkap dengan Foto Sesuai 100%
$products = [
    // --- RUMAH TANGGA ---
    [
        'category' => 'rumah-tangga',
        'store_slug' => 'yiqii-official-store',
        'name' => 'YIQII 50cm/60cm Lemari Pakaian Plastik Lipat Portabel Transparan',
        'slug' => 'yiqii-50cm-60cm-lemari-pakaian-plastik-transparan',
        'description' => 'Lemari penyimpanan bertingkat YIQII dengan pintu transparan, tidak perlu instalasi rumit, kapasitas besar, dilengkapi roda senyap. Praktis dan elegan untuk kamar tidur.',
        'price' => 240000, // Harga dasar seller
        'discount_percentage' => 66, // Diskon 66% sesuai referensi
        'stock' => 150,
        'rating' => 5.0,
        'sold_count' => 52400,
        'image' => 'https://images.unsplash.com/photo-1595428774223-ef52624120d2?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'rumah-tangga',
        'store_slug' => 'yiqii-official-store',
        'name' => 'Philips Air Fryer HD9200 Rapid Air Technology 4.1L Low Watt',
        'slug' => 'philips-air-fryer-hd9200-4-1l',
        'description' => 'Air fryer Philips teknologi Rapid Air menggoreng dengan sedikit atau tanpa minyak. Kapasitas 4.1 Liter, hemat listrik, mudah dibersihkan.',
        'price' => 780000,
        'discount_percentage' => 30,
        'stock' => 85,
        'rating' => 4.9,
        'sold_count' => 12800,
        'image' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'rumah-tangga',
        'store_slug' => 'yiqii-official-store',
        'name' => 'Set Wajan Panci Keramik Anti Lengket Marble Coating 5 Pcs',
        'slug' => 'set-wajan-panci-keramik-marble-5pcs',
        'description' => 'Set panci granit anti lengket bebas PFOA/PTFE, gagang kayu tahan panas, lapisan marble tebal dan mudah dicuci.',
        'price' => 350000,
        'discount_percentage' => 40,
        'stock' => 110,
        'rating' => 4.8,
        'sold_count' => 8500,
        'image' => 'https://images.unsplash.com/photo-1584990347449-397223b20755?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- HANDPHONE & TABLET ---
    [
        'category' => 'handphone-tablet',
        'store_slug' => 'gadget-gallery-id',
        'name' => 'iPhone 15 Pro Max 256GB - Natural Titanium Garansi Resmi iBox',
        'slug' => 'iphone-15-pro-max-256gb-natural-titanium',
        'description' => 'Chip A17 Pro bertenaga monster, bodi titanium grade dirgantara, kamera utama 48MP dengan 5x optical zoom, layar 120Hz ProMotion.',
        'price' => 19500000,
        'discount_percentage' => 10,
        'stock' => 40,
        'rating' => 5.0,
        'sold_count' => 3400,
        'image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'handphone-tablet',
        'store_slug' => 'gadget-gallery-id',
        'name' => 'Samsung Galaxy S24 Ultra 5G 12GB/512GB - Titanium Gray',
        'slug' => 'samsung-galaxy-s24-ultra-512gb',
        'description' => 'Galaxy AI cerdas terintegrasi, S Pen presisi, kamera 200MP Nightography, Snapdragon 8 Gen 3 for Galaxy.',
        'price' => 18200000,
        'discount_percentage' => 12,
        'stock' => 35,
        'rating' => 4.9,
        'sold_count' => 2800,
        'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'handphone-tablet',
        'store_slug' => 'gadget-gallery-id',
        'name' => 'iPad Pro 11 Inch M4 OLED 256GB Wi-Fi - Space Black',
        'slug' => 'ipad-pro-11-inch-m4-oled-256gb',
        'description' => 'Layar Ultra Retina XDR Tandem OLED tertipis di dunia dengan Chip Apple M4 performa luar biasa.',
        'price' => 14800000,
        'discount_percentage' => 8,
        'stock' => 25,
        'rating' => 5.0,
        'sold_count' => 1950,
        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&auto=format&fit=crop&q=80',
        'badge' => 'hot',
        'is_featured' => true,
    ],

    // --- GAMING ---
    [
        'category' => 'gaming',
        'store_slug' => 'techzone-authorized',
        'name' => 'Pulsar X2 Wireless Gaming Mouse Superlight 59g - White',
        'slug' => 'pulsar-x2-wireless-gaming-mouse-white',
        'description' => 'Mouse gaming kompetitif ultra ringan tanpa lubang, sensor optik PixArt PAW3395 26.000 DPI, switch optik tahan double click.',
        'price' => 950000,
        'discount_percentage' => 15,
        'stock' => 80,
        'rating' => 4.9,
        'sold_count' => 6400,
        'image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => true,
    ],
    [
        'category' => 'gaming',
        'store_slug' => 'techzone-authorized',
        'name' => 'Logitech G Pro X Superlight 2 Wireless Gaming Mouse - Black',
        'slug' => 'logitech-g-pro-x-superlight-2-black',
        'description' => 'Standar mouse pro player esport, switch LIGHTFORCE hybrid optik-mekanikal, sensor HERO 2, USB-C fast charging.',
        'price' => 1900000,
        'discount_percentage' => 18,
        'stock' => 50,
        'rating' => 5.0,
        'sold_count' => 9800,
        'image' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'gaming',
        'store_slug' => 'techzone-authorized',
        'name' => 'PlayStation 5 Slim Digital Edition 1TB SSD Garansi Resmi Sony',
        'slug' => 'playstation-5-slim-digital-edition-1tb',
        'description' => 'Konsol gaming masa kini dengan SSD berkecepatan ultra tinggi 1TB, ray tracing realistis, audio 3D Tempest, DualSense controller.',
        'price' => 6800000,
        'discount_percentage' => 10,
        'stock' => 30,
        'rating' => 5.0,
        'sold_count' => 4100,
        'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'gaming',
        'store_slug' => 'techzone-authorized',
        'name' => 'Razer BlackWidow V4 Pro Mechanical Gaming Keyboard RGB',
        'slug' => 'razer-blackwidow-v4-pro-mechanical-keyboard',
        'description' => 'Keyboard mekanikal dengan Razer Green Switch kliky tactile, underglow lighting RGB Chroma, roller multimedia multifungsi.',
        'price' => 2800000,
        'discount_percentage' => 22,
        'stock' => 45,
        'rating' => 4.9,
        'sold_count' => 3200,
        'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&auto=format&fit=crop&q=80',
        'badge' => 'hot',
        'is_featured' => false,
    ],

    // --- KOMPUTER & LAPTOP ---
    [
        'category' => 'komputer-laptop',
        'store_slug' => 'techzone-authorized',
        'name' => 'Apple MacBook Pro 14 Inch M3 Pro 18GB/512GB SSD - Space Black',
        'slug' => 'apple-macbook-pro-14-m3-pro-space-black',
        'description' => 'Laptop profesional terkuat dengan chip Apple M3 Pro 11-core CPU & 14-core GPU, layar Liquid Retina XDR 120Hz, baterai hingga 18 jam.',
        'price' => 31500000,
        'discount_percentage' => 5,
        'stock' => 15,
        'rating' => 5.0,
        'sold_count' => 1200,
        'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'komputer-laptop',
        'store_slug' => 'techzone-authorized',
        'name' => 'ASUS ROG Zephyrus G14 OLED Ryzen 9 RTX 4060 16GB/1TB',
        'slug' => 'asus-rog-zephyrus-g14-oled-rtx4060',
        'description' => 'Laptop gaming bodi all-aluminum tertipis dan teringan, layar ROG Nebula OLED 3K 120Hz, pendingin vapor chamber.',
        'price' => 24500000,
        'discount_percentage' => 8,
        'stock' => 20,
        'rating' => 4.9,
        'sold_count' => 850,
        'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&auto=format&fit=crop&q=80',
        'badge' => 'hot',
        'is_featured' => false,
    ],
    [
        'category' => 'komputer-laptop',
        'store_slug' => 'techzone-authorized',
        'name' => 'Monitor Gaming 27 Inch 2K IPS 180Hz 1ms HDR FreeSync',
        'slug' => 'monitor-gaming-27-inch-2k-ips-180hz',
        'description' => 'Monitor bezel-less resolusi QHD 2560x1440, panel Fast IPS 180Hz, waktu respon 1ms GTG, sRGB 99% warna akurat.',
        'price' => 2650000,
        'discount_percentage' => 25,
        'stock' => 60,
        'rating' => 4.8,
        'sold_count' => 4600,
        'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => true,
    ],

    // --- FASHION PRIA ---
    [
        'category' => 'fashion-pria',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Erigo T-Shirt Oversize Graphic Vintage Cotton Combed 24s',
        'slug' => 'erigo-t-shirt-oversize-graphic-vintage',
        'description' => 'Kaos oversized streetwear bahan 100% katun combed 24s premium tebal, sablon plastisol tahan cuci, adem dan nyaman seharian.',
        'price' => 145000,
        'discount_percentage' => 50,
        'stock' => 300,
        'rating' => 4.9,
        'sold_count' => 45000,
        'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'fashion-pria',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Kemeja Oxford Pria Lengan Panjang Slim Fit Premium - Navy',
        'slug' => 'kemeja-oxford-pria-lengan-panjang-navy',
        'description' => 'Kemeja pria kasual formal bahan katun Oxford adem tidak mudah kusut, jahitan rantai rapi, potongan slim fit modern.',
        'price' => 195000,
        'discount_percentage' => 35,
        'stock' => 180,
        'rating' => 4.8,
        'sold_count' => 15200,
        'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],
    [
        'category' => 'fashion-pria',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Ventela Public Low White Sneakers Pria & Wanita Original',
        'slug' => 'ventela-public-low-white-sneakers',
        'description' => 'Sepatu sneakers lokal legendaris sol karet vulkanisir tahan lama, insole ultralite foam empuk, bahan canvas 12oz tebal.',
        'price' => 290000,
        'discount_percentage' => 30,
        'stock' => 220,
        'rating' => 5.0,
        'sold_count' => 38000,
        'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],

    // --- FASHION WANITA ---
    [
        'category' => 'fashion-wanita',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Dress Wanita Casual A-Line Korean Floral Chiffon Premium',
        'slug' => 'dress-wanita-casual-a-line-korean-floral',
        'description' => 'Dress midi gaya Korea motif bunga elegan dengan bahan sifon sutra halus berlapis furing, kerah V-neck cantik dan tali pinggang.',
        'price' => 260000,
        'discount_percentage' => 45,
        'stock' => 140,
        'rating' => 4.9,
        'sold_count' => 18500,
        'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'fashion-wanita',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Tas Selempang Wanita Kulit Sintetis Vintage Shoulder Bag - Brown',
        'slug' => 'tas-selempang-wanita-kulit-vintage-brown',
        'description' => 'Tas bahu wanita model retro bahan kulit sintetis PU premium waterproof, kompartemen luas dengan resleting anti macet.',
        'price' => 220000,
        'discount_percentage' => 40,
        'stock' => 95,
        'rating' => 4.8,
        'sold_count' => 11200,
        'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- KECANTIKAN ---
    [
        'category' => 'kecantikan',
        'store_slug' => 'glow-beauty-care',
        'name' => 'SKINTIFIC 5X Ceramide Barrier Moisture Gel 30g Pelembab Wajah',
        'slug' => 'skintific-5x-ceramide-barrier-moisture-gel',
        'description' => 'Moisturizer viral perbaiki skin barrier dengan 5 tipe Ceramide, Hyaluronic Acid, dan Centella Asiatica. Tekstur watery gel dingin menyerap cepat.',
        'price' => 169000,
        'discount_percentage' => 32,
        'stock' => 400,
        'rating' => 5.0,
        'sold_count' => 88000,
        'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'kecantikan',
        'store_slug' => 'glow-beauty-care',
        'name' => 'Serum Wajah Niacinamide 10% + Zinc 1% Brightening 30ml',
        'slug' => 'serum-wajah-niacinamide-10-zinc-1-brightening',
        'description' => 'Serum pencerah kulit wajah efektif samarkan noda hitam bekas jerawat, kontrol minyak berlebih dan perkecil tampilan pori-pori.',
        'price' => 115000,
        'discount_percentage' => 38,
        'stock' => 250,
        'rating' => 4.9,
        'sold_count' => 42000,
        'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&auto=format&fit=crop&q=80',
        'badge' => 'hot',
        'is_featured' => false,
    ],
    [
        'category' => 'kecantikan',
        'store_slug' => 'glow-beauty-care',
        'name' => 'Lipstick Matte Velvet Long Lasting Waterproof 12 Jam - Berry',
        'slug' => 'lipstick-matte-velvet-long-lasting-berry',
        'description' => 'Lipstick cair formula velvet matte lembut tidak membuat bibir kering, transferproof, tahan hingga 12 jam dengan warna intens.',
        'price' => 85000,
        'discount_percentage' => 40,
        'stock' => 320,
        'rating' => 4.8,
        'sold_count' => 29000,
        'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- KESEHATAN ---
    [
        'category' => 'kesehatan',
        'store_slug' => 'healthy-life-store',
        'name' => 'Vitamin C 1000mg + Zinc 60 Tablet Daya Tahan Tubuh',
        'slug' => 'vitamin-c-1000mg-zinc-60-tablet',
        'description' => 'Suplemen multivitamin daya tahan tubuh kombinasi Vitamin C murni dan Zinc mineral penting untuk perlindungan imun setiap hari.',
        'price' => 95000,
        'discount_percentage' => 25,
        'stock' => 500,
        'rating' => 5.0,
        'sold_count' => 31000,
        'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'kesehatan',
        'store_slug' => 'healthy-life-store',
        'name' => 'Masker Medis 3 Ply BFE 99% Surgical Earloop 50 pcs - Blue',
        'slug' => 'masker-medis-3-ply-bfe-99-surgical-50pcs',
        'description' => 'Masker bedah medis 3 lapis standar kemenkes RI, filtrasi bakteri 99%, kawat hidung fleksibel dan tali earloop nyaman di telinga.',
        'price' => 45000,
        'discount_percentage' => 35,
        'stock' => 800,
        'rating' => 4.9,
        'sold_count' => 64000,
        'image' => 'https://images.unsplash.com/photo-1584634731339-252c581abfc5?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- OLAHRAGA ---
    [
        'category' => 'olahraga',
        'store_slug' => 'sport-station-id',
        'name' => 'Sepatu Lari Nike Air Zoom Pegasus 40 - Black Metallic',
        'slug' => 'sepatu-lari-nike-air-zoom-pegasus-40-black',
        'description' => 'Sepatu lari ikonik dengan bantalan responsif ganda Zoom Air di depan dan tumit, upper mesh berpori sirkulasi udara maksimal.',
        'price' => 1850000,
        'discount_percentage' => 20,
        'stock' => 70,
        'rating' => 5.0,
        'sold_count' => 7800,
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'olahraga',
        'store_slug' => 'sport-station-id',
        'name' => 'Matras Yoga TPE Premium 8mm Anti Slip Eco Friendly Free Bag',
        'slug' => 'matras-yoga-tpe-premium-8mm-anti-slip',
        'description' => 'Matras senam yoga dan pilates bahan TPE ramah lingkungan tebal 8mm empuk menjaga sendi, tekstur dual side anti-slip.',
        'price' => 175000,
        'discount_percentage' => 30,
        'stock' => 150,
        'rating' => 4.9,
        'sold_count' => 14200,
        'image' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],
    [
        'category' => 'olahraga',
        'store_slug' => 'sport-station-id',
        'name' => 'Dumbbell Set Adjustable 20kg Rubber Coated Home Gym',
        'slug' => 'dumbbell-set-adjustable-20kg-rubber-coated',
        'description' => 'Set barbel dumbbell dapat diubah menjadi barbell panjang dengan sambungan busa empuk, lapisan karet pelindung lantai.',
        'price' => 480000,
        'discount_percentage' => 28,
        'stock' => 90,
        'rating' => 4.8,
        'sold_count' => 6100,
        'image' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&auto=format&fit=crop&q=80',
        'badge' => 'hot',
        'is_featured' => false,
    ],

    // --- MAKANAN & MINUMAN ---
    [
        'category' => 'makanan-minuman',
        'store_slug' => 'yiqii-official-store',
        'name' => 'Kopi Arabica Gayo Roasted Coffee Beans 250gr Specialty Grade',
        'slug' => 'kopi-arabica-gayo-roasted-coffee-beans-250gr',
        'description' => 'Biji kopi single origin Arabika Dataran Tinggi Gayo Aceh, profil sangrai medium roast dengan aroma floral dan dark chocolate gurih.',
        'price' => 98000,
        'discount_percentage' => 22,
        'stock' => 300,
        'rating' => 5.0,
        'sold_count' => 22000,
        'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=600&auto=format&fit=crop&q=80',
        'badge' => 'bestseller',
        'is_featured' => true,
    ],
    [
        'category' => 'makanan-minuman',
        'store_slug' => 'healthy-life-store',
        'name' => 'Madu Murni Randu Organik 100% Raw Honey 500gr Nektar Alami',
        'slug' => 'madu-murni-randu-organik-raw-honey-500gr',
        'description' => 'Madu murni alami tanpa pemanasan dan tanpa pengawet atau pemanis buatan, kaya enzim alami penjaga kesehatan dan stamina.',
        'price' => 110000,
        'discount_percentage' => 20,
        'stock' => 180,
        'rating' => 4.9,
        'sold_count' => 16500,
        'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- BUKU & ALAT TULIS ---
    [
        'category' => 'buku-alat-tulis',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Buku Agenda Jurnal A5 Dotted Bullet Journal Hardcover 160 Hal',
        'slug' => 'buku-agenda-jurnal-a5-dotted-bullet-journal',
        'description' => 'Buku tulis dotted premium kertas 100gsm tebal anti tembus tinta, sampul kulit sintetis hardcover elegan dengan pita pembatas.',
        'price' => 65000,
        'discount_percentage' => 30,
        'stock' => 350,
        'rating' => 4.9,
        'sold_count' => 19000,
        'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],
    [
        'category' => 'buku-alat-tulis',
        'store_slug' => 'urban-style-apparel',
        'name' => 'Set Pulpen Gel 0.5mm 12 Warna Pastel Smooth Quick Dry Ink',
        'slug' => 'set-pulpen-gel-0-5mm-12-warna-pastel',
        'description' => 'Set pena gel warna pastel cantik dengan ujung nib 0.5mm lancar, tinta cepat kering tidak bleber di kertas, grip silikon empuk.',
        'price' => 45000,
        'discount_percentage' => 35,
        'stock' => 450,
        'rating' => 4.8,
        'sold_count' => 27500,
        'image' => 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- OTOMOTIF ---
    [
        'category' => 'otomotif',
        'store_slug' => 'otospeed-performance',
        'name' => 'Oli Mesin Mobil Full Synthetic SAE 5W-30 API SP 4 Liter',
        'slug' => 'oli-mesin-mobil-full-synthetic-5w-30-4l',
        'description' => 'Pelumas mesin mobil bensin teknologi full synthetic terkini, perlindungan optimal terhadap gesekan dan keausan, efisiensi bahan bakar maksimal.',
        'price' => 420000,
        'discount_percentage' => 18,
        'stock' => 80,
        'rating' => 4.9,
        'sold_count' => 9200,
        'image' => 'https://images.unsplash.com/photo-1635784063737-142f36113bba?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'otomotif',
        'store_slug' => 'otospeed-performance',
        'name' => 'Ban Motor Tubeless Soft Compound 90/80-17 Racing Grip',
        'slug' => 'ban-motor-tubeless-soft-compound-90-80-17',
        'description' => 'Ban luar motor tubeless kompon lunak daya cengkeram tinggi di jalan basah maupun kering, alur pola pembuangan air presisi.',
        'price' => 295000,
        'discount_percentage' => 25,
        'stock' => 120,
        'rating' => 4.8,
        'sold_count' => 13400,
        'image' => 'https://images.unsplash.com/photo-1578844251758-2f71da64c96f?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => false,
    ],

    // --- ELEKTRONIK ---
    [
        'category' => 'elektronik',
        'store_slug' => 'techzone-authorized',
        'name' => 'Sony WH-1000XM5 Wireless Noise Canceling Headphones - Silver',
        'slug' => 'sony-wh-1000xm5-wireless-headphones-silver',
        'description' => 'Headphone peredam bising terbaik di industri dengan 8 mikrofon & prosesor V1/QN1, audio resolusi tinggi LDAC, baterai 30 jam.',
        'price' => 4500000,
        'discount_percentage' => 15,
        'stock' => 40,
        'rating' => 5.0,
        'sold_count' => 5600,
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
        'badge' => 'official',
        'is_featured' => true,
    ],
    [
        'category' => 'elektronik',
        'store_slug' => 'techzone-authorized',
        'name' => 'Smart TV 43 Inch 4K UHD Dolby Audio Android TV HDR10',
        'slug' => 'smart-tv-43-inch-4k-uhd-android-tv',
        'description' => 'Televisi pintar layar 43 inci 4K Ultra HD tajam berbingkai tipis, audio Dolby Atmos menggelegar, Google Assistant dan Netflix Youtube resmi.',
        'price' => 3200000,
        'discount_percentage' => 20,
        'stock' => 35,
        'rating' => 4.9,
        'sold_count' => 3900,
        'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=600&auto=format&fit=crop&q=80',
        'badge' => 'sale',
        'is_featured' => true,
    ],
];

$count = 0;
foreach ($products as $pData) {
    $categorySlug = $pData['category'];
    $storeSlug = $pData['store_slug'];
    unset($pData['category'], $pData['store_slug']);

    if (isset($categories[$categorySlug])) {
        $pData['category_id'] = $categories[$categorySlug]->id;
        $pData['store_id'] = isset($stores[$storeSlug]) ? $stores[$storeSlug]->id : $defaultStore->id;
        $pData['is_active'] = true;

        $created = Product::create($pData);
        $count++;
        echo "[$count] {$created->name} (Kategori: {$categorySlug}) -> Selesai ✅\n";
    }
}

echo "\n🎉 SELESAI! Sebanyak {$count} produk dari 13 kategori lengkap dengan FOTO COCOK 100% berhasil dimasukkan ke database!\n";
