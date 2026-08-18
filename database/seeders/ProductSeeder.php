<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone Android Flagship 2026',
                'description' => 'Smartphone flagship dengan chipset terbaru, kamera 108MP, layar AMOLED 6.7 inch 120Hz, RAM 12GB, storage 256GB. Dilengkapi dengan fast charging 65W dan wireless charging.',
                'price' => 8999000,
                'discount_percentage' => 15,
                'rating' => 4.8,
                'sold_count' => 1250,
                'stock' => 45,
                'badge' => 'new',
                'is_featured' => true,
            ],
            [
                'name' => 'Laptop Gaming RGB Mechanical Keyboard',
                'description' => 'Laptop gaming dengan RTX 4060, Intel Core i7 Gen 13, RAM 16GB, SSD 512GB. Layar 15.6" 144Hz. Termasuk cooling pad dan mouse gaming.',
                'price' => 15999000,
                'discount_percentage' => 20,
                'rating' => 4.9,
                'sold_count' => 890,
                'stock' => 23,
                'badge' => 'bestseller',
                'is_featured' => true,
            ],
            [
                'name' => 'Sepatu Sneakers Running Sport Premium',
                'description' => 'Sepatu olahraga dengan teknologi cushioning terbaru, breathable mesh, lightweight design. Cocok untuk running, gym, dan aktivitas sehari-hari.',
                'price' => 899000,
                'discount_percentage' => 30,
                'rating' => 4.7,
                'sold_count' => 2340,
                'stock' => 156,
                'badge' => 'sale',
                'is_featured' => false,
            ],
            [
                'name' => 'Smartwatch Fitness Tracker GPS',
                'description' => 'Smartwatch dengan GPS built-in, heart rate monitor, sleep tracking, waterproof IP68. Baterai tahan hingga 14 hari. Kompatibel iOS dan Android.',
                'price' => 1299000,
                'discount_percentage' => 25,
                'rating' => 4.6,
                'sold_count' => 1567,
                'stock' => 78,
                'badge' => null,
                'is_featured' => true,
            ],
            [
                'name' => 'Wireless Earbuds ANC Pro Max',
                'description' => 'TWS earbuds dengan Active Noise Cancellation, transparency mode, wireless charging case. Kualitas audio Hi-Fi, baterai 6 jam (30 jam dengan case).',
                'price' => 699000,
                'discount_percentage' => 35,
                'rating' => 4.5,
                'sold_count' => 3420,
                'stock' => 234,
                'badge' => 'hot',
                'is_featured' => false,
            ],
            [
                'name' => 'Tas Ransel Anti Maling USB Port',
                'description' => 'Tas ransel laptop dengan fitur anti maling, USB charging port, bahan water resistant, kompartemen laptop 15.6", cocok untuk travel dan kuliah.',
                'price' => 299000,
                'discount_percentage' => 40,
                'rating' => 4.4,
                'sold_count' => 4560,
                'stock' => 450,
                'badge' => 'sale',
                'is_featured' => false,
            ],
            [
                'name' => 'Kamera Mirrorless 4K Video Vlog',
                'description' => 'Kamera mirrorless 24MP, video 4K 60fps, flip screen untuk vlogging, WiFi dan Bluetooth. Include lens kit 16-50mm OSS.',
                'price' => 12500000,
                'discount_percentage' => 10,
                'rating' => 4.9,
                'sold_count' => 456,
                'stock' => 12,
                'badge' => 'new',
                'is_featured' => true,
            ],
            [
                'name' => 'Mechanical Keyboard RGB Gaming',
                'description' => 'Keyboard mechanical dengan switch blue, full RGB backlight, aluminium body, anti-ghosting. Cocok untuk gaming dan typing.',
                'price' => 850000,
                'discount_percentage' => 20,
                'rating' => 4.7,
                'sold_count' => 1890,
                'stock' => 89,
                'badge' => null,
                'is_featured' => false,
            ],
            [
                'name' => 'Monitor Gaming 27" 165Hz Curved',
                'description' => 'Monitor gaming curved 27 inch, resolusi 2K QHD, refresh rate 165Hz, response time 1ms, HDR10, FreeSync Premium.',
                'price' => 3499000,
                'discount_percentage' => 15,
                'rating' => 4.8,
                'sold_count' => 678,
                'stock' => 34,
                'badge' => 'bestseller',
                'is_featured' => true,
            ],
            [
                'name' => 'Jaket Hoodie Premium Cotton',
                'description' => 'Jaket hoodie unisex bahan cotton fleece premium, nyaman dipakai, tersedia berbagai warna. Cocok untuk casual dan streetwear.',
                'price' => 249000,
                'discount_percentage' => 50,
                'rating' => 4.3,
                'sold_count' => 5670,
                'stock' => 890,
                'badge' => 'sale',
                'is_featured' => false,
            ],
        ];

        // Ambil store dan category pertama untuk contoh
        $store = \App\Models\Store::where('status', 'approved')->first();
        $categories = \App\Models\Category::all();

        if (!$store || $categories->isEmpty()) {
            $this->command->warn('Tidak ada store yang approved atau category. Silakan buat terlebih dahulu.');
            return;
        }

        foreach ($products as $productData) {
            // Random category
            $category = $categories->random();

            \App\Models\Product::create([
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => \Illuminate\Support\Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'discount_percentage' => $productData['discount_percentage'],
                'rating' => $productData['rating'],
                'sold_count' => $productData['sold_count'],
                'stock' => $productData['stock'],
                'image' => null, // Nanti diisi dengan foto asli Anda
                'images' => null, // Nanti diisi dengan array foto tambahan
                'badge' => $productData['badge'],
                'is_active' => true,
                'is_featured' => $productData['is_featured'],
            ]);
        }

        $this->command->info('✅ Berhasil membuat ' . count($products) . ' produk demo!');
        $this->command->info('📸 Silakan upload foto produk Anda sendiri melalui panel seller.');
    }
}
