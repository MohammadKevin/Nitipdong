<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

// Create Super Admin
$superAdmin = User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@belanjain.test',
    'password' => Hash::make('password'),
    'role' => 'super_admin',
    'phone' => '081234567890',
    'email_verified_at' => now(),
]);

// Create Seller
$seller = User::create([
    'name' => 'Seller BelanjaIn',
    'email' => 'seller@belanjain.test',
    'password' => Hash::make('password'),
    'role' => 'seller',
    'phone' => '081234567891',
    'email_verified_at' => now(),
]);

// Create Store
$store = Store::create([
    'user_id' => $seller->id,
    'name' => 'Official BelanjaIn Store',
    'slug' => 'official-belanjain-store',
    'description' => 'Toko resmi BelanjaIn dengan produk original dan terpercaya',
    'address' => 'Jakarta, Indonesia',
    'phone' => '081234567891',
    'status' => 'approved',
]);

// Create Categories
$categories = [
    ['name' => 'Elektronik', 'slug' => 'elektronik', 'icon' => 'fa-solid fa-plug'],
    ['name' => 'Handphone & Tablet', 'slug' => 'handphone-tablet', 'icon' => 'fa-solid fa-mobile-screen-button'],
    ['name' => 'Komputer & Laptop', 'slug' => 'komputer-laptop', 'icon' => 'fa-solid fa-laptop'],
    ['name' => 'Gaming', 'slug' => 'gaming', 'icon' => 'fa-solid fa-gamepad'],
    ['name' => 'Fashion Pria', 'slug' => 'fashion-pria', 'icon' => 'fa-solid fa-user-tie'],
    ['name' => 'Fashion Wanita', 'slug' => 'fashion-wanita', 'icon' => 'fa-solid fa-person-dress'],
    ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'icon' => 'fa-solid fa-heart-pulse'],
    ['name' => 'Kecantikan', 'slug' => 'kecantikan', 'icon' => 'fa-solid fa-spa'],
    ['name' => 'Olahraga', 'slug' => 'olahraga', 'icon' => 'fa-solid fa-dumbbell'],
    ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'icon' => 'fa-solid fa-house'],
    ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman', 'icon' => 'fa-solid fa-utensils'],
    ['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis', 'icon' => 'fa-solid fa-book'],
    ['name' => 'Otomotif', 'slug' => 'otomotif', 'icon' => 'fa-solid fa-car'],
];

foreach ($categories as $cat) {
    Category::create($cat);
}

$phoneCat = Category::where('slug', 'handphone-tablet')->first();
$gamingCat = Category::where('slug', 'gaming')->first();

// Create Products
Product::create([
    'store_id' => $store->id,
    'category_id' => $phoneCat->id,
    'name' => 'iPhone 15 Pro Max 256GB - Natural Titanium',
    'slug' => 'iphone-15-pro-max-256gb',
    'description' => 'iPhone 15 Pro Max dengan chip A17 Pro, kamera 48MP, dan layar Super Retina XDR 6.7 inci.',
    'price' => 19000000, // Harga seller (customer akan bayar 19950000)
    'stock' => 50,
    'image' => 'img/iphone-15-pro-max.jpg',
    'is_active' => true,
]);

Product::create([
    'store_id' => $store->id,
    'category_id' => $phoneCat->id,
    'name' => 'Samsung Galaxy S24 Ultra 512GB - Titanium Black',
    'slug' => 'samsung-galaxy-s24-ultra-512gb',
    'description' => 'Samsung S24 Ultra dengan S Pen, kamera 200MP, dan layar Dynamic AMOLED 6.8 inci.',
    'price' => 18000000,
    'stock' => 30,
    'image' => 'img/samsung-s24-ultra.jpg',
    'is_active' => true,
]);

Product::create([
    'store_id' => $store->id,
    'category_id' => $gamingCat->id,
    'name' => 'Pulsar X2 Wireless Gaming Mouse - Medium Size',
    'slug' => 'pulsar-x2-wireless-gaming-mouse',
    'description' => 'Gaming mouse wireless ultra ringan 59g dengan sensor PAW3395, polling rate 1000Hz, dan battery life hingga 70 jam.',
    'price' => 950000,
    'stock' => 100,
    'image' => 'img/pulsar-x-susanto.jpg',
    'is_active' => true,
]);

// Create Customer
$customer = User::create([
    'name' => 'Customer Test',
    'email' => 'customer@belanjain.test',
    'password' => Hash::make('password'),
    'role' => 'customer',
    'phone' => '081234567892',
    'email_verified_at' => now(),
]);

echo "\n✅ SQLite Database seeded successfully!\n\n";
echo "Accounts created:\n";
echo "  Super Admin: superadmin@belanjain.test / password\n";
echo "  Seller: seller@belanjain.test / password\n";
echo "  Customer: customer@belanjain.test / password\n\n";
echo "Products created: 3\n";
echo "  - iPhone 15 Pro Max\n";
echo "  - Samsung S24 Ultra\n";
echo "  - Pulsar X2 Mouse\n\n";
