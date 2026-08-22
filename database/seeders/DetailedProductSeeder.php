<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Store, Category, Product};
use Illuminate\Support\Str;

class DetailedProductSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada seller dan store
        $store = $this->ensureStoreExists();

        // Pastikan categories ada
        $this->ensureCategories();

        // Data produk lengkap
        $products = $this->getProducts();

        $createdCount = 0;
        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();

            if (!$category) {
                $category = Category::inRandomOrder()->first();
            }

            Product::create([
                'uuid' => (string) Str::uuid(),
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . Str::random(5),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'discount_percentage' => $productData['discount_percentage'],
                'rating' => $productData['rating'],
                'sold_count' => $productData['sold_count'],
                'stock' => $productData['stock'],
                'image' => $productData['image'] ?? null,
                'images' => $productData['images'] ?? null,
                'badge' => $productData['badge'],
                'is_active' => true,
                'is_featured' => $productData['is_featured'],
            ]);

            $createdCount++;
        }

        $this->displaySummary($createdCount, $products);
    }

    private function ensureStoreExists(): Store
    {
        $store = Store::where('status', 'approved')->first();

        if (!$store) {
            $seller = User::firstOrCreate(
                ['email' => 'seller@belanjain.com'],
                [
                    'name' => 'NitipDong Official Store',
                    'password' => bcrypt('password'),
                    'role' => 'seller',
                    'email_verified_at' => now(),
                ]
            );

            $store = Store::create([
                'user_id' => $seller->id,
                'name' => 'NitipDong Official Store',
                'slug' => 'nitipdong-official',
                'description' => 'Toko resmi dengan produk berkualitas dan harga terbaik se-Indonesia',
                'status' => 'approved',
                'city' => 'Jakarta Pusat',
            ]);
        }

        return $store;
    }

    private function ensureCategories(): void
    {
        $categories = ['Elektronik', 'Pakaian', 'Makanan', 'Otomotif'];

        foreach ($categories as $catName) {
            Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );
        }
    }

    private function getProducts(): array
    {
        return [
            // ELEKTRONIK
            [
                'name' => 'iPhone 15 Pro Max 256GB Natural Titanium',
                'description' => 'iPhone 15 Pro Max dengan chip A17 Pro, kamera 48MP dengan 5x optical zoom, layar Super Retina XDR 6.7", ProMotion 120Hz, Dynamic Island, baterai hingga 29 jam video playback. Frame titanium premium dengan desain paling ringan. Sudah termasuk: USB-C cable, dokumentasi. Garansi resmi iBox 1 tahun.',
                'price' => 23999000,
                'discount_percentage' => 5,
                'rating' => 4.95,
                'sold_count' => 856,
                'stock' => 15,
                'badge' => 'new',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Samsung Galaxy S24 Ultra 12/512GB Titanium Black',
                'description' => 'Samsung Galaxy S24 Ultra dengan Snapdragon 8 Gen 3, kamera 200MP, layar Dynamic AMOLED 2X 6.8" 120Hz, S Pen built-in, Galaxy AI features. Baterai 5000mAh dengan fast charging 45W. Design titanium premium tahan gores. Paket: charger 45W, cable USB-C, S Pen tips, clear case. SEIN 1 tahun.',
                'price' => 21999000,
                'discount_percentage' => 8,
                'rating' => 4.92,
                'sold_count' => 1243,
                'stock' => 23,
                'badge' => 'bestseller',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'MacBook Pro 14" M3 Pro 18GB/512GB Space Black',
                'description' => 'MacBook Pro 14 inch dengan chip M3 Pro (12-core CPU, 18-core GPU), RAM 18GB unified memory, SSD 512GB. Layar Liquid Retina XDR 14.2" dengan ProMotion 120Hz, 3 Thunderbolt 4 ports, HDMI, SD card slot, MagSafe 3. Baterai hingga 18 jam. Garansi resmi Apple Indonesia 1 tahun. Gratis: USB-C to MagSafe cable, 96W adapter.',
                'price' => 39999000,
                'discount_percentage' => 3,
                'rating' => 4.98,
                'sold_count' => 432,
                'stock' => 8,
                'badge' => 'new',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'ASUS ROG Strix G16 RTX 4060 i7-13650HX 16GB/512GB',
                'description' => 'Laptop gaming ASUS ROG dengan processor Intel Core i7-13650HX (14-core), NVIDIA GeForce RTX 4060 8GB, RAM 16GB DDR5 4800MHz (upgradeable), SSD 512GB NVMe PCIe 4.0. Layar 16" FHD 165Hz, RGB keyboard per-key, cooling system dual-fan. Windows 11 Home + Office. Bonus: gaming mouse ROG, backpack ROG. Garansi 2 tahun.',
                'price' => 19999000,
                'discount_percentage' => 12,
                'rating' => 4.87,
                'sold_count' => 967,
                'stock' => 31,
                'badge' => 'hot',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'iPad Pro 11" M2 WiFi 128GB Space Gray',
                'description' => 'iPad Pro 11 inch dengan chip M2 8-core, Liquid Retina display 11" ProMotion 120Hz, kamera 12MP + LiDAR, Face ID, 4 speakers. Mendukung Apple Pencil Gen 2 dan Magic Keyboard. Baterai hingga 10 jam. iOS terbaru dengan Stage Manager. Cocok untuk design, editing, dan produktivitas. Garansi resmi iBox 1 tahun.',
                'price' => 14999000,
                'discount_percentage' => 7,
                'rating' => 4.91,
                'sold_count' => 724,
                'stock' => 42,
                'badge' => null,
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones Black',
                'description' => 'Headphone premium Sony dengan ANC terbaik di kelasnya, 8 microphones untuk crystal clear calls, 30mm driver baru, LDAC & Hi-Res Audio, multipoint connection. Baterai 30 jam dengan ANC on, quick charge 3 menit = 3 jam. Ultra comfortable fit dengan soft fit leather. Include: carrying case premium, cable 3.5mm, USB-C cable. Garansi resmi TAM 1 tahun.',
                'price' => 5299000,
                'discount_percentage' => 15,
                'rating' => 4.89,
                'sold_count' => 2145,
                'stock' => 67,
                'badge' => 'bestseller',
                'is_featured' => false,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Apple Watch Series 9 GPS 45mm Midnight Aluminium',
                'description' => 'Apple Watch Series 9 dengan chip S9 SiP, display always-on Retina LTPO OLED 2000 nits, double tap gesture. Health features: ECG, blood oxygen, sleep tracking, temperature sensing. Fitness tracking 100+ workout types. Crash detection & Fall detection. Water resistant 50m. Baterai 18 jam. Strap: Sport Band Midnight. Garansi resmi iBox 1 tahun.',
                'price' => 7999000,
                'discount_percentage' => 10,
                'rating' => 4.93,
                'sold_count' => 1534,
                'stock' => 28,
                'badge' => 'new',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'DJI Mini 4 Pro Fly More Combo',
                'description' => 'Drone DJI Mini 4 Pro dengan kamera 48MP 4K/60fps HDR, sensor 1/1.3" CMOS, gimbal 3-axis, omnidirectional obstacle sensing, ActiveTrack 360°, berat hanya 249g (no license needed). Fly More Combo include: 3 baterai intelligent flight (total 102 menit), charging hub, shoulder bag, propeller guard. Controller RC-N2. Garansi resmi DJI Indonesia 1 tahun.',
                'price' => 14499000,
                'discount_percentage' => 5,
                'rating' => 4.96,
                'sold_count' => 389,
                'stock' => 12,
                'badge' => 'new',
                'is_featured' => true,
                'category' => 'Elektronik',
                'image' => 'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // FASHION & PAKAIAN
            [
                'name' => 'Nike Air Max 90 Premium White Black Original',
                'description' => 'Sepatu sneakers Nike Air Max 90 original dengan teknologi Air cushioning, upper premium leather & mesh breathable, midsole foam empuk, outsole rubber durable. Design iconic retro modern. Tersedia size 40-45 (US 7-11). Sudah termasuk: box original, extra laces, paper stuffing. 100% original dengan QR authenticity. Garansi sole 6 bulan. Perfect untuk daily wear dan casual style.',
                'price' => 1899000,
                'discount_percentage' => 25,
                'rating' => 4.86,
                'sold_count' => 3421,
                'stock' => 234,
                'badge' => 'bestseller',
                'is_featured' => true,
                'category' => 'Pakaian',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Adidas Ultraboost 22 Running Shoes Black Solar Yellow',
                'description' => 'Sepatu running Adidas Ultraboost 22 dengan teknologi Boost cushioning responsif, Primeknit+ upper fit seperti kaus kaki, Continental rubber outsole grip maksimal, support frame LEP 2.0. Energy return 20% lebih tinggi. Cocok untuk long distance running dan daily training. Size chart: 40-46. Include: box original, shoebag, extra insole. Authenticity guaranteed. Garansi manufacturing defect 3 bulan.',
                'price' => 2699000,
                'discount_percentage' => 15,
                'rating' => 4.92,
                'sold_count' => 1876,
                'stock' => 156,
                'badge' => 'hot',
                'is_featured' => true,
                'category' => 'Pakaian',
                'image' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Champion Reverse Weave Hoodie Grey Heather',
                'description' => 'Hoodie Champion Reverse Weave premium dengan heavyweight 350gsm fleece, anti-shrink technology (no shrinkage after wash), ribbed side panels untuk durability, kangaroo pocket, adjustable drawcord hood, ribbed cuffs & hem. Logo Champion classic embroidered. Ukuran: S-XXL (oversized fit). Composition: 82% cotton 18% polyester. Machine washable. USA authentic quality. Comfort & durability terjamin.',
                'price' => 899000,
                'discount_percentage' => 30,
                'rating' => 4.85,
                'sold_count' => 4231,
                'stock' => 678,
                'badge' => 'bestseller',
                'is_featured' => false,
                'category' => 'Pakaian',
                'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Ray-Ban Wayfarer Classic Black Green G-15 RB2140',
                'description' => 'Kacamata Ray-Ban Wayfarer original dengan frame acetate premium glossy black, lensa crystal glass G-15 (100% UV protection), iconic square shape. Lens size: 50mm, bridge: 22mm, temple: 150mm. Include: original case Ray-Ban, cleaning cloth, authenticity card, booklet. Made in Italy. Serial number engraved. Timeless design sejak 1956. Perfect untuk fashion & eye protection.',
                'price' => 2199000,
                'discount_percentage' => 12,
                'rating' => 4.96,
                'sold_count' => 2145,
                'stock' => 123,
                'badge' => 'bestseller',
                'is_featured' => true,
                'category' => 'Pakaian',
                'image' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // MAKANAN & MINUMAN
            [
                'name' => 'Kopi Arabica Gayo Aceh Premium 1kg Biji/Bubuk',
                'description' => 'Kopi arabica single origin dari dataran tinggi Gayo Aceh dengan altitude 1200-1600 mdpl. Proses full washed, roast level medium (city roast). Flavor notes: dark chocolate, brown sugar, mild spice. Acidity: medium, body: full, sweetness: high. Tersedia whole beans atau ground (pilih saat order). Roast date tercantum di kemasan. Best before: 6 bulan dari roast date. Kemasan: valve zipper bag foil. 100% arabica, no mix.',
                'price' => 185000,
                'discount_percentage' => 15,
                'rating' => 4.91,
                'sold_count' => 3456,
                'stock' => 567,
                'badge' => 'bestseller',
                'is_featured' => true,
                'category' => 'Makanan',
                'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Madu Hutan Liar Murni Sumbawa 500ml',
                'description' => 'Madu hutan liar 100% murni dari hutan Sumbawa, dipanen langsung oleh masyarakat lokal dari sarang lebah liar (Apis dorsata). Tidak dipanaskan, tidak ada campuran gula/sirup. Warna: gelap keemasan, tekstur: kental natural, rasa: manis kompleks dengan aroma floral & woody. Manfaat: antioksidan tinggi, antibacterial, boost immunity. Kemasan: botol kaca 500ml. BPOM & Halal MUI. Best before: 2 tahun. Free sendok kayu.',
                'price' => 125000,
                'discount_percentage' => 20,
                'rating' => 4.88,
                'sold_count' => 2345,
                'stock' => 234,
                'badge' => 'hot',
                'is_featured' => true,
                'category' => 'Makanan',
                'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            [
                'name' => 'Teh Hijau Matcha Premium Japan Grade A 100g',
                'description' => 'Matcha powder premium dari Uji, Japan dengan grade ceremonial (highest quality). Bright vibrant green color, fine powder texture smooth, umami flavor rich dengan natural sweetness. Shade-grown tencha leaves, stone-ground traditional method. Perfect untuk matcha latte, baking, smoothies, ice cream. Caffeine content: medium-high. Antioksidan super tinggi (EGCG). Kemasan: tin can kedap udara. Made in Japan. Best before: 12 bulan. Include bamboo scoop.',
                'price' => 299000,
                'discount_percentage' => 10,
                'rating' => 4.93,
                'sold_count' => 1234,
                'stock' => 156,
                'badge' => null,
                'is_featured' => true,
                'category' => 'Makanan',
                'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&auto=format&fit=crop&q=80',
                ],
            ],

            // OTOMOTIF
            [
                'name' => 'Helm Full Face AGV K1 Rossi Mugello 2016 Replica',
                'description' => 'Helm full face AGV K1 dengan desain Rossi Mugello 2016 replica. Shell thermoplastic high-resistance, visor class optical 1 anti-scratch, ventilation system 5 air vents, interior fabric antiseptic removable & washable, double D-ring retention system. DOT & ECE certified. Weight: ±1.5kg. Size: S(55-56), M(57-58), L(59-60), XL(61-62). Include: helmet bag, clear visor spare, chin curtain. Garansi shell 1 tahun. Premium quality replica dengan safety standard.',
                'price' => 1899000,
                'discount_percentage' => 20,
                'rating' => 4.87,
                'sold_count' => 1234,
                'stock' => 67,
                'badge' => 'hot',
                'is_featured' => true,
                'category' => 'Otomotif',
                'image' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&auto=format&fit=crop&q=80',
                ],
            ],
        ];
    }

    private function displaySummary(int $createdCount, array $products): void
    {
        $elektronik = count(array_filter($products, fn($p) => $p['category'] === 'Elektronik'));
        $pakaian = count(array_filter($products, fn($p) => $p['category'] === 'Pakaian'));
        $makanan = count(array_filter($products, fn($p) => $p['category'] === 'Makanan'));
        $otomotif = count(array_filter($products, fn($p) => $p['category'] === 'Otomotif'));

        $this->command->info('');
        $this->command->info('✅ Berhasil membuat ' . $createdCount . ' produk lengkap dengan foto HD!');
        $this->command->info('');
        $this->command->line('📦 Detail Produk:');
        $this->command->line('   • ' . $elektronik . ' produk Elektronik');
        $this->command->line('   • ' . $pakaian . ' produk Fashion & Pakaian');
        $this->command->line('   • ' . $makanan . ' produk Makanan & Minuman');
        $this->command->line('   • ' . $otomotif . ' produk Otomotif');
        $this->command->info('');
    }
}
