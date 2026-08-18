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
                'image' => null,
                'images' => null,
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
                    'name' => 'BelanjaIn Official Store',
                    'password' => bcrypt('password'),
                    'role' => 'seller',
                    'email_verified_at' => now(),
                ]
            );

            $store = Store::create([
                'user_id' => $seller->id,
                'name' => 'BelanjaIn Official Store',
                'slug' => 'belanjain-official',
                'description' => 'Toko resmi dengan produk berkualitas dan harga terbaik',
                'status' => 'approved',
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
            ['name' => 'iPhone 15 Pro Max 256GB Natural Titanium', 'description' => 'iPhone 15 Pro Max dengan chip A17 Pro, kamera 48MP dengan 5x optical zoom, layar Super Retina XDR 6.7", ProMotion 120Hz, Dynamic Island, baterai hingga 29 jam video playback. Frame titanium premium dengan desain paling ringan. Sudah termasuk: USB-C cable, dokumentasi. Garansi resmi iBox 1 tahun.', 'price' => 23999000, 'discount_percentage' => 5, 'rating' => 4.95, 'sold_count' => 856, 'stock' => 15, 'badge' => 'new', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'Samsung Galaxy S24 Ultra 12/512GB Titanium Black', 'description' => 'Samsung Galaxy S24 Ultra dengan Snapdragon 8 Gen 3, kamera 200MP, layar Dynamic AMOLED 2X 6.8" 120Hz, S Pen built-in, Galaxy AI features. Baterai 5000mAh dengan fast charging 45W. Design titanium premium tahan gores. Paket: charger 45W, cable USB-C, S Pen tips, clear case. SEIN 1 tahun.', 'price' => 21999000, 'discount_percentage' => 8, 'rating' => 4.92, 'sold_count' => 1243, 'stock' => 23, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'MacBook Pro 14" M3 Pro 18GB/512GB Space Black', 'description' => 'MacBook Pro 14 inch dengan chip M3 Pro (12-core CPU, 18-core GPU), RAM 18GB unified memory, SSD 512GB. Layar Liquid Retina XDR 14.2" dengan ProMotion 120Hz, 3 Thunderbolt 4 ports, HDMI, SD card slot, MagSafe 3. Baterai hingga 18 jam. Garansi resmi Apple Indonesia 1 tahun. Gratis: USB-C to MagSafe cable, 96W adapter.', 'price' => 39999000, 'discount_percentage' => 3, 'rating' => 4.98, 'sold_count' => 432, 'stock' => 8, 'badge' => 'new', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'ASUS ROG Strix G16 RTX 4060 i7-13650HX 16GB/512GB', 'description' => 'Laptop gaming ASUS ROG dengan processor Intel Core i7-13650HX (14-core), NVIDIA GeForce RTX 4060 8GB, RAM 16GB DDR5 4800MHz (upgradeable), SSD 512GB NVMe PCIe 4.0. Layar 16" FHD 165Hz, RGB keyboard per-key, cooling system dual-fan. Windows 11 Home + Office. Bonus: gaming mouse ROG, backpack ROG. Garansi 2 tahun.', 'price' => 19999000, 'discount_percentage' => 12, 'rating' => 4.87, 'sold_count' => 967, 'stock' => 31, 'badge' => 'hot', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'iPad Pro 11" M2 WiFi 128GB Space Gray', 'description' => 'iPad Pro 11 inch dengan chip M2 8-core, Liquid Retina display 11" ProMotion 120Hz, kamera 12MP + LiDAR, Face ID, 4 speakers. Mendukung Apple Pencil Gen 2 dan Magic Keyboard. Baterai hingga 10 jam. iOS terbaru dengan Stage Manager. Cocok untuk design, editing, dan produktivitas. Garansi resmi iBox 1 tahun.', 'price' => 14999000, 'discount_percentage' => 7, 'rating' => 4.91, 'sold_count' => 724, 'stock' => 42, 'badge' => null, 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones Black', 'description' => 'Headphone premium Sony dengan ANC terbaik di kelasnya, 8 microphones untuk crystal clear calls, 30mm driver baru, LDAC & Hi-Res Audio, multipoint connection. Baterai 30 jam dengan ANC on, quick charge 3 menit = 3 jam. Ultra comfortable fit dengan soft fit leather. Include: carrying case premium, cable 3.5mm, USB-C cable. Garansi resmi TAM 1 tahun.', 'price' => 5299000, 'discount_percentage' => 15, 'rating' => 4.89, 'sold_count' => 2145, 'stock' => 67, 'badge' => 'bestseller', 'is_featured' => false, 'category' => 'Elektronik'],

            ['name' => 'Apple Watch Series 9 GPS 45mm Midnight Aluminium', 'description' => 'Apple Watch Series 9 dengan chip S9 SiP, display always-on Retina LTPO OLED 2000 nits, double tap gesture. Health features: ECG, blood oxygen, sleep tracking, temperature sensing. Fitness tracking 100+ workout types. Crash detection & Fall detection. Water resistant 50m. Baterai 18 jam. Strap: Sport Band Midnight. Garansi resmi iBox 1 tahun.', 'price' => 7999000, 'discount_percentage' => 10, 'rating' => 4.93, 'sold_count' => 1534, 'stock' => 28, 'badge' => 'new', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'DJI Mini 4 Pro Fly More Combo', 'description' => 'Drone DJI Mini 4 Pro dengan kamera 48MP 4K/60fps HDR, sensor 1/1.3" CMOS, gimbal 3-axis, omnidirectional obstacle sensing, ActiveTrack 360°, berat hanya 249g (no license needed). Fly More Combo include: 3 baterai intelligent flight (total 102 menit), charging hub, shoulder bag, propeller guard. Controller RC-N2. Garansi resmi DJI Indonesia 1 tahun.', 'price' => 14499000, 'discount_percentage' => 5, 'rating' => 4.96, 'sold_count' => 389, 'stock' => 12, 'badge' => 'new', 'is_featured' => true, 'category' => 'Elektronik'],

            ['name' => 'Canon EOS R6 Mark II Body Only Mirrorless Camera', 'description' => 'Kamera mirrorless Canon EOS R6 Mark II dengan sensor full-frame 24.2MP CMOS, processor DIGIC X, continuous shooting 40fps electronic / 12fps mechanical, video 4K 60p uncropped, 6K RAW oversampling. Dual Pixel CMOS AF II dengan Eye/Animal/Vehicle detection. IBIS 8-stop. 2 card slots (SD UHS-II). Garansi resmi Datascrip 1 tahun. Body only (lensa terpisah).', 'price' => 37999000, 'discount_percentage' => 0, 'rating' => 4.97, 'sold_count' => 178, 'stock' => 6, 'badge' => null, 'is_featured' => false, 'category' => 'Elektronik'],

            ['name' => 'LG C3 OLED evo 55" 4K Smart TV 2024', 'description' => 'TV OLED LG C3 55 inch dengan self-lit OLED evo panel, processor α9 Gen6 AI, 4K 120Hz gaming (HDMI 2.1), Dolby Vision IQ, Dolby Atmos, webOS 23 smart platform, ThinQ AI, Apple AirPlay 2, Google Assistant & Alexa. Gaming features: VRR, G-Sync, FreeSync, ALLM. Perfect untuk PS5/Xbox. Garansi panel 5 tahun resmi LG Indonesia. Include: Magic Remote, wall mount.', 'price' => 19999000, 'discount_percentage' => 20, 'rating' => 4.94, 'sold_count' => 542, 'stock' => 18, 'badge' => 'sale', 'is_featured' => true, 'category' => 'Elektronik'],

            // FASHION & PAKAIAN
            ['name' => 'Nike Air Max 90 Premium White Black Original', 'description' => 'Sepatu sneakers Nike Air Max 90 original dengan teknologi Air cushioning, upper premium leather & mesh breathable, midsole foam empuk, outsole rubber durable. Design iconic retro modern. Tersedia size 40-45 (US 7-11). Sudah termasuk: box original, extra laces, paper stuffing. 100% original dengan QR authenticity. Garansi sole 6 bulan. Perfect untuk daily wear dan casual style.', 'price' => 1899000, 'discount_percentage' => 25, 'rating' => 4.86, 'sold_count' => 3421, 'stock' => 234, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'Adidas Ultraboost 22 Running Shoes Black Solar Yellow', 'description' => 'Sepatu running Adidas Ultraboost 22 dengan teknologi Boost cushioning responsif, Primeknit+ upper fit seperti kaus kaki, Continental rubber outsole grip maksimal, support frame LEP 2.0. Energy return 20% lebih tinggi. Cocok untuk long distance running dan daily training. Size chart: 40-46. Include: box original, shoebag, extra insole. Authenticity guaranteed. Garansi manufacturing defect 3 bulan.', 'price' => 2699000, 'discount_percentage' => 15, 'rating' => 4.92, 'sold_count' => 1876, 'stock' => 156, 'badge' => 'hot', 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'Uniqlo AIRism Cotton Oversized T-Shirt Navy', 'description' => 'Kaos Uniqlo AIRism dengan teknologi fabric quick-dry, anti-bacterial, smooth & cool to touch. Bahan cotton blend premium 100% breathable. Model oversized trendy fit. Detail: crew neck, dropped shoulder, relaxed fit. Ukuran: S, M, L, XL, XXL (size chart lengkap di foto). Care: machine washable 30°C. Made in Vietnam. 100% original Uniqlo Indonesia. Nyaman untuk cuaca tropis.', 'price' => 149000, 'discount_percentage' => 50, 'rating' => 4.78, 'sold_count' => 8945, 'stock' => 1245, 'badge' => 'sale', 'is_featured' => false, 'category' => 'Pakaian'],

            ['name' => 'Levi\'s 501 Original Fit Jeans Dark Stonewash', 'description' => 'Celana jeans Levi\'s 501 original fit iconic dengan 100% cotton denim 13.5oz, button fly, straight leg from hip to ankle. Warna dark stonewash timeless. Durable construction dengan rivets & bartacks. Red Tab authentic. Size: 28-38 (W) x 30-34 (L). Made in Pakistan. Care instruction: cold wash inside out. Garansi keaslian 100%. Perfect investment piece untuk wardrobe essential.', 'price' => 1299000, 'discount_percentage' => 20, 'rating' => 4.88, 'sold_count' => 2567, 'stock' => 389, 'badge' => null, 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'The North Face Nuptse 1996 Jacket Black', 'description' => 'Jaket The North Face Nuptse 1996 retro iconic dengan 700-fill goose down insulation super warm, nylon ripstop fabric tahan air & angin, non-removable hood, elastic cuffs, hem cinch-cord, 2 hand pockets + 1 chest pocket. Perfect untuk cuaca dingin 0-15°C. Size: S, M, L, XL (fit chart detail di foto). Include: TNF dust bag, care card. Authenticity tag hologram. Limited stock item.', 'price' => 4599000, 'discount_percentage' => 10, 'rating' => 4.94, 'sold_count' => 876, 'stock' => 45, 'badge' => 'hot', 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'Champion Reverse Weave Hoodie Grey Heather', 'description' => 'Hoodie Champion Reverse Weave premium dengan heavyweight 350gsm fleece, anti-shrink technology (no shrinkage after wash), ribbed side panels untuk durability, kangaroo pocket, adjustable drawcord hood, ribbed cuffs & hem. Logo Champion classic embroidered. Ukuran: S-XXL (oversized fit). Composition: 82% cotton 18% polyester. Machine washable. USA authentic quality. Comfort & durability terjamin.', 'price' => 899000, 'discount_percentage' => 30, 'rating' => 4.85, 'sold_count' => 4231, 'stock' => 678, 'badge' => 'bestseller', 'is_featured' => false, 'category' => 'Pakaian'],

            ['name' => 'New Balance 2002R Protection Pack Grey', 'description' => 'Sneakers New Balance 2002R dengan ABZORB midsole cushioning, N-ergy outsole shock absorption, premium pigskin & mesh upper, stability web technology. Retro running aesthetic meets modern comfort. Colorway: grey with navy & white accent. Size: 40-45. Include: original box NB, paper stuffing, extra laces, care card. Made in Vietnam. QR code verification. Cocok untuk sneakerheads & daily outfit.', 'price' => 1799000, 'discount_percentage' => 18, 'rating' => 4.91, 'sold_count' => 1534, 'stock' => 234, 'badge' => null, 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'Ray-Ban Wayfarer Classic Black Green G-15 RB2140', 'description' => 'Kacamata Ray-Ban Wayfarer original dengan frame acetate premium glossy black, lensa crystal glass G-15 (100% UV protection), iconic square shape. Lens size: 50mm, bridge: 22mm, temple: 150mm. Include: original case Ray-Ban, cleaning cloth, authenticity card, booklet. Made in Italy. Serial number engraved. Timeless design sejak 1956. Perfect untuk fashion & eye protection.', 'price' => 2199000, 'discount_percentage' => 12, 'rating' => 4.96, 'sold_count' => 2145, 'stock' => 123, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Pakaian'],

            ['name' => 'Converse Chuck Taylor All Star 70s High Black', 'description' => 'Sepatu Converse Chuck 70 premium dengan canvas 12oz heavyweight, ortholite insole premium cushion, egret midsole vintage look, higher rubber foxing, tonal stitching detail. Ankle patch All Star vintage, license plate. True to size. Size: 37-45. Include: box original, hang tag. Made in Vietnam. QR authenticity. Icon sneaker dengan comfort upgrade. Perfect untuk streetwear & vintage style.', 'price' => 1099000, 'discount_percentage' => 20, 'rating' => 4.87, 'sold_count' => 5678, 'stock' => 456, 'badge' => null, 'is_featured' => false, 'category' => 'Pakaian'],

            // MAKANAN
            ['name' => 'Kopi Arabica Gayo Aceh Premium 1kg Biji/Bubuk', 'description' => 'Kopi arabica single origin dari dataran tinggi Gayo Aceh dengan altitude 1200-1600 mdpl. Proses full washed, roast level medium (city roast). Flavor notes: dark chocolate, brown sugar, mild spice. Acidity: medium, body: full, sweetness: high. Tersedia whole beans atau ground (pilih saat order). Roast date tercantum di kemasan. Best before: 6 bulan dari roast date. Kemasan: valve zipper bag foil. 100% arabica, no mix.', 'price' => 185000, 'discount_percentage' => 15, 'rating' => 4.91, 'sold_count' => 3456, 'stock' => 567, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Makanan'],

            ['name' => 'Madu Hutan Liar Murni Sumbawa 500ml', 'description' => 'Madu hutan liar 100% murni dari hutan Sumbawa, dipanen langsung oleh masyarakat lokal dari sarang lebah liar (Apis dorsata). Tidak dipanaskan, tidak ada campuran gula/sirup. Warna: gelap keemasan, tekstur: kental natural, rasa: manis kompleks dengan aroma floral & woody. Manfaat: antioksidan tinggi, antibacterial, boost immunity. Kemasan: botol kaca 500ml. BPOM & Halal MUI. Best before: 2 tahun. Free sendok kayu.', 'price' => 125000, 'discount_percentage' => 20, 'rating' => 4.88, 'sold_count' => 2345, 'stock' => 234, 'badge' => 'hot', 'is_featured' => true, 'category' => 'Makanan'],

            ['name' => 'Teh Hijau Matcha Premium Japan Grade A 100g', 'description' => 'Matcha powder premium dari Uji, Japan dengan grade ceremonial (highest quality). Bright vibrant green color, fine powder texture smooth, umami flavor rich dengan natural sweetness. Shade-grown tencha leaves, stone-ground traditional method. Perfect untuk matcha latte, baking, smoothies, ice cream. Caffeine content: medium-high. Antioksidan super tinggi (EGCG). Kemasan: tin can kedap udara. Made in Japan. Best before: 12 bulan. Include bamboo scoop.', 'price' => 299000, 'discount_percentage' => 10, 'rating' => 4.93, 'sold_count' => 1234, 'stock' => 156, 'badge' => null, 'is_featured' => true, 'category' => 'Makanan'],

            ['name' => 'Dark Chocolate 85% Cacao Premium Belgium 250g', 'description' => 'Dark chocolate premium Belgium dengan 85% cocoa content. Single origin cacao beans dari Ecuador. Flavor profile: intense cocoa, subtle fruity notes, low sugar. Texture: smooth melting. Ingredients: cocoa mass, cocoa butter, sugar, vanilla. No milk, no soy lecithin. Vegan friendly. Rich in antioxidants & minerals. Packaging: foil wrap + paper box. Halal certified. Best before: 18 bulan. Storage: cool dry place 18-20°C. Perfect untuk chocolate lovers & healthy lifestyle.', 'price' => 159000, 'discount_percentage' => 30, 'rating' => 4.89, 'sold_count' => 2876, 'stock' => 456, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Makanan'],

            // OTOMOTIF
            ['name' => 'Ban Motor Michelin Pilot Street 2 110/70-17 & 140/70-17', 'description' => 'Paket ban motor Michelin Pilot Street 2 ukuran depan 110/70-17 54S dan belakang 140/70-17 66S. Teknologi Michelin 2CT+ dual compound untuk grip optimal & durability. Silica-based compound untuk wet grip. Pattern design khusus sport street dengan water evacuation excellent. Cocok untuk motor sport 150-250cc (Vixion, CBR, Ninja, dll). Made in Indonesia pabrik Michelin. DOT code fresh (produksi <6 bulan). Sudah termasuk pentil tubeless.', 'price' => 1249000, 'discount_percentage' => 15, 'rating' => 4.92, 'sold_count' => 3456, 'stock' => 89, 'badge' => 'bestseller', 'is_featured' => true, 'category' => 'Otomotif'],

            ['name' => 'Helm Full Face AGV K1 Rossi Mugello 2016 Replica', 'description' => 'Helm full face AGV K1 dengan desain Rossi Mugello 2016 replica. Shell thermoplastic high-resistance, visor class optical 1 anti-scratch, ventilation system 5 air vents, interior fabric antiseptic removable & washable, double D-ring retention system. DOT & ECE certified. Weight: ±1.5kg. Size: S(55-56), M(57-58), L(59-60), XL(61-62). Include: helmet bag, clear visor spare, chin curtain. Garansi shell 1 tahun. Premium quality replica dengan safety standard.', 'price' => 1899000, 'discount_percentage' => 20, 'rating' => 4.87, 'sold_count' => 1234, 'stock' => 67, 'badge' => 'hot', 'is_featured' => true, 'category' => 'Otomotif'],

            ['name' => 'Oli Motor Shell Advance AX7 10W-40 Matic 1 Liter', 'description' => 'Oli motor Shell Advance AX7 10W-40 semi-synthetic untuk motor matic (skutik). Teknologi Active Cleansing dengan low ash formula mencegah deposit di piston & ring. Viscosity stable pada suhu tinggi. Protection terhadap wear & tear mesin matic. API SL, JASO MB certified. Cocok untuk: Beat, Vario, Mio, Aerox, Scoopy, dll. Kemasan: botol 1 liter original sealed. Hologram authenticity. Exp date: printed on bottle. Made in Indonesia under Shell license.', 'price' => 89000, 'discount_percentage' => 25, 'rating' => 4.84, 'sold_count' => 8934, 'stock' => 1234, 'badge' => 'sale', 'is_featured' => false, 'category' => 'Otomotif'],
        ];
    }

    private function displaySummary(int $createdCount, array $products): void
    {
        $elektronik = count(array_filter($products, fn($p) => $p['category'] === 'Elektronik'));
        $pakaian = count(array_filter($products, fn($p) => $p['category'] === 'Pakaian'));
        $makanan = count(array_filter($products, fn($p) => $p['category'] === 'Makanan'));
        $otomotif = count(array_filter($products, fn($p) => $p['category'] === 'Otomotif'));

        $this->command->info('');
        $this->command->info('✅ Berhasil membuat ' . $createdCount . ' produk lengkap!');
        $this->command->info('');
        $this->command->line('📦 Detail Produk:');
        $this->command->line('   • ' . $elektronik . ' produk Elektronik');
        $this->command->line('   • ' . $pakaian . ' produk Fashion & Pakaian');
        $this->command->line('   • ' . $makanan . ' produk Makanan & Minuman');
        $this->command->line('   • ' . $otomotif . ' produk Otomotif');
        $this->command->info('');
        $this->command->line('📸 Langkah Selanjutnya:');
        $this->command->line('   1. Login: seller@belanjain.com / password');
        $this->command->line('   2. Buka "Kelola Produk"');
        $this->command->line('   3. Edit produk & upload foto Anda');
        $this->command->line('   4. Upload 1 foto utama + max 5 foto tambahan');
        $this->command->info('');
        $this->command->line('💡 Tips: Gunakan foto min 800x800px dengan background bersih!');
        $this->command->info('');
    }
}
