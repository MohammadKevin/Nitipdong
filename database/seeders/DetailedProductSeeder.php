<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Store, Category, Product};
use Illuminate\Support\Str;

class DetailedProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Inisialisasi Kategori
        $categories = $this->createCategories();

        // 2. Data Toko & Produk (Minimal 5-6 Produk per Toko)
        $storeDataList = $this->getStoresAndProducts();

        $totalStores = 0;
        $totalProducts = 0;

        foreach ($storeDataList as $item) {
            $seller = User::firstOrCreate(
                ['email' => $item['seller_email']],
                [
                    'name'              => $item['seller_name'],
                    'password'          => bcrypt('password'),
                    'role'              => 'seller',
                    'phone'             => $item['phone'],
                    'email_verified_at' => now(),
                ]
            );

            $store = Store::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'user_id'     => $seller->id,
                    'name'        => $item['name'],
                    'description' => $item['description'],
                    'status'      => 'approved',
                    'city'        => $item['city'],
                    'address'     => $item['address'],
                    'logo'        => $item['logo'],
                    'banner'      => $item['banner'],
                ]
            );

            $totalStores++;

            $catModel = $categories[$item['category_key']] ?? $categories['Elektronik'];

            foreach ($item['products'] as $prod) {
                Product::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name'     => $prod['name'],
                    ],
                    [
                        'uuid'                => (string) Str::uuid(),
                        'category_id'         => $catModel->id,
                        'slug'                => Str::slug($prod['name']) . '-' . Str::random(4),
                        'description'         => $prod['description'],
                        'price'               => $prod['price'],
                        'discount_percentage' => $prod['discount_percentage'],
                        'rating'              => 0.0,
                        'sold_count'          => 0,
                        'stock'               => $prod['stock'],
                        'image'               => $prod['image'],
                        'images'              => $prod['images'] ?? [$prod['image']],
                        'badge'               => $prod['badge'],
                        'is_active'           => true,
                        'is_featured'         => $prod['is_featured'] ?? false,
                    ]
                );
                $totalProducts++;
            }
        }

        $this->command->info('');
        $this->command->info("✅ Berhasil membuat {$totalStores} Toko Resmi & {$totalProducts} Produk Demo HD!");
        $this->command->line("   • Eiger Adventure Official (Outdoor & Fashion) - 6 Produk");
        $this->command->line("   • Apple iBox Official Store (Elektronik & Gadget) - 6 Produk");
        $this->command->line("   • Nike Official Store Indonesia (Pakaian & Olahraga) - 6 Produk");
        $this->command->line("   • Kopi Nusantara & Makanan Sehat (Makanan & Minuman) - 6 Produk");
        $this->command->line("   • Autospeed Racing & Garage (Otomotif & Aksesoris) - 6 Produk");
        $this->command->info('');
    }

    private function createCategories(): array
    {
        $cats = [
            'Outdoor'    => ['name' => 'Outdoor & Petualangan', 'slug' => 'outdoor-petualangan'],
            'Elektronik' => ['name' => 'Elektronik & Gadget',   'slug' => 'elektronik-gadget'],
            'Pakaian'    => ['name' => 'Pakaian & Olahraga',     'slug' => 'pakaian-olahraga'],
            'Makanan'    => ['name' => 'Makanan & Minuman',      'slug' => 'makanan-minuman'],
            'Otomotif'   => ['name' => 'Otomotif & Aksesoris',   'slug' => 'otomotif-aksesoris'],
        ];

        $models = [];
        foreach ($cats as $key => $data) {
            $models[$key] = Category::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name']]
            );
        }

        return $models;
    }

    private function getStoresAndProducts(): array
    {
        return [
            // ══════════════════════════════════════════════════
            // 1. EIGER ADVENTURE OFFICIAL STORE (OUTDOOR)
            // ══════════════════════════════════════════════════
            [
                'name'         => 'Eiger Adventure Official',
                'slug'         => 'eiger-adventure-official',
                'seller_name'  => 'Eigerindo Multi Produk',
                'seller_email' => 'seller.eiger@nitipdong.com',
                'phone'        => '081298765431',
                'city'         => 'Bandung',
                'address'      => 'Jl. Sumatera No. 23, Kota Bandung, Jawa Barat',
                'description'  => 'Toko resmi Eiger Adventure Indonesia. Perlengkapan mendaki, tas ransel carrier, jaket outdoor, dan gear petualangan terbaik.',
                'category_key' => 'Outdoor',
                'logo'         => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=200&auto=format&fit=crop&q=80',
                'banner'       => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1200&auto=format&fit=crop&q=80',
                'products'     => [
                    [
                        'name' => 'Eiger Carrier Rhinos 60L Hiking Backpack Black',
                        'description' => 'Tas gunung carrier Eiger kapasitas 60L dengan sistem suspensi backsystem ergonomis, bahan Cordura ripstop ultra durable, include raincover. Cocok untuk pendakian 3-5 hari.',
                        'price' => 1299000,
                        'discount_percentage' => 10,
                        'rating' => 4.9,
                        'sold_count' => 1420,
                        'stock' => 35,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                        'images' => [
                            'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1577733966973-d680bffd2e80?w=800&auto=format&fit=crop&q=80',
                        ],
                    ],
                    [
                        'name' => 'Eiger Jacket WS X-Torrent Waterproof Windproof',
                        'description' => 'Jaket outdoor Eiger wanita dan pria dengan teknologi Tropic Waterproof 10.000mm, breathable mesh inner, seam-sealed taped, hoodie adjustable. Melindungi dari hujan badai dan angin kencang.',
                        'price' => 849000,
                        'discount_percentage' => 15,
                        'rating' => 4.85,
                        'sold_count' => 980,
                        'stock' => 50,
                        'badge' => 'hot',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1548883354-7622d03aca27?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Eiger Pollock Mid Cut Waterproof Hiking Boots',
                        'description' => 'Sepatu gunung Eiger dengan upper nubuck leather & Tropic Waterproof membrane, outsole Vibram grip anti slip di medan bebatuan dan lumpur. Insole Ortholite empuk anti lecet.',
                        'price' => 1499000,
                        'discount_percentage' => 20,
                        'rating' => 4.92,
                        'sold_count' => 640,
                        'stock' => 28,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Eiger Shiraishi Quickdry Stretch Cargo Pants Olive',
                        'description' => 'Celana panjang outdoor Eiger bahan Tropic Repellent quick dry yang cepat kering saat basah, elastis lentur bergerak bebas, 6 kantong cargo luas.',
                        'price' => 429000,
                        'discount_percentage' => 5,
                        'rating' => 4.75,
                        'sold_count' => 2100,
                        'stock' => 110,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Eiger X-Tormentor Tactical Waist Bag 4L',
                        'description' => 'Tas pinggang selempang waistbag Eiger dengan kompartemen utama luas, organizer barang esensial, resleting YKK waterproof, material polyester 600D kuat.',
                        'price' => 259000,
                        'discount_percentage' => 25,
                        'rating' => 4.88,
                        'sold_count' => 3890,
                        'stock' => 200,
                        'badge' => 'sale',
                        'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Eiger Fortress 4 Person Camping Dome Tent',
                        'description' => 'Tenda camping kapasitas 4 orang dengan vestibule teras depan luas, frame aluminium alloy kokoh tahan angin gunung, flysheet 3000mm waterproof PU coating.',
                        'price' => 2199000,
                        'discount_percentage' => 10,
                        'rating' => 4.95,
                        'sold_count' => 310,
                        'stock' => 15,
                        'badge' => 'new',
                        'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════
            // 2. APPLE IBOX OFFICIAL STORE (ELEKTRONIK)
            // ══════════════════════════════════════════════════
            [
                'name'         => 'iBox Apple Official Store',
                'slug'         => 'ibox-apple-official-store',
                'seller_name'  => 'PT Erajaya Swasembada Tbk',
                'seller_email' => 'seller.ibox@nitipdong.com',
                'phone'        => '081298765432',
                'city'         => 'Jakarta Selatan',
                'address'      => 'Mall Pacific Place Lt. 2, SCBD, Jakarta Selatan',
                'description'  => 'Reseller resmi produk Apple di Indonesia. iPhone, iPad, Mac, Apple Watch, AirPods dengan garansi resmi 1 tahun.',
                'category_key' => 'Elektronik',
                'logo'         => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=200&auto=format&fit=crop&q=80',
                'banner'       => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=1200&auto=format&fit=crop&q=80',
                'products'     => [
                    [
                        'name' => 'Apple iPhone 15 Pro Max 256GB Natural Titanium',
                        'description' => 'iPhone 15 Pro Max chip A17 Pro, kamera 48MP dengan 5x optical zoom, layar Super Retina XDR 6.7" ProMotion 120Hz, bodi Titanium ringan. Garansi resmi iBox 1 tahun.',
                        'price' => 23999000,
                        'discount_percentage' => 5,
                        'rating' => 4.95,
                        'sold_count' => 856,
                        'stock' => 25,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
                        'images' => [
                            'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=80',
                        ],
                    ],
                    [
                        'name' => 'Apple MacBook Pro 14" M3 Pro 18GB/512GB Space Black',
                        'description' => 'MacBook Pro 14 inch dengan chip M3 Pro (12-core CPU, 18-core GPU), RAM 18GB, SSD 512GB. Layar Liquid Retina XDR 120Hz, baterai hingga 18 jam.',
                        'price' => 39999000,
                        'discount_percentage' => 3,
                        'rating' => 4.98,
                        'sold_count' => 432,
                        'stock' => 12,
                        'badge' => 'new',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Apple iPad Pro 11" M2 WiFi 128GB Space Gray',
                        'description' => 'iPad Pro 11 inch chip M2, Liquid Retina display 120Hz ProMotion, kamera 12MP + LiDAR sensor. Mendukung Apple Pencil 2 dan Magic Keyboard.',
                        'price' => 14999000,
                        'discount_percentage' => 7,
                        'rating' => 4.91,
                        'sold_count' => 724,
                        'stock' => 30,
                        'badge' => 'hot',
                        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Apple Watch Series 9 GPS 45mm Midnight Aluminium',
                        'description' => 'Apple Watch Series 9 chip S9 SiP, layar Always-On 2000 nits, fitur Double Tap gesture, sensor ECG, blood oxygen, dan crash detection.',
                        'price' => 7999000,
                        'discount_percentage' => 10,
                        'rating' => 4.93,
                        'sold_count' => 1534,
                        'stock' => 40,
                        'badge' => 'bestseller',
                        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Apple AirPods Pro Gen 2 USB-C MagSafe Case',
                        'description' => 'AirPods Pro Gen 2 dengan Active Noise Cancellation 2x lebih kuat, Adaptive Audio, Transparency mode, dan casing pengisian daya USB-C MagSafe.',
                        'price' => 3999000,
                        'discount_percentage' => 12,
                        'rating' => 4.94,
                        'sold_count' => 3210,
                        'stock' => 85,
                        'badge' => 'bestseller',
                        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Apple Magic Keyboard with Touch ID & Numeric Keypad',
                        'description' => 'Keyboard nirkabel Apple dengan sensor Touch ID untuk login cepat dan aman, keypad numerik lengkap, baterai tahan sebulan sekali charge.',
                        'price' => 2799000,
                        'discount_percentage' => 5,
                        'rating' => 4.86,
                        'sold_count' => 510,
                        'stock' => 20,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════
            // 3. NIKE OFFICIAL STORE INDONESIA (FASHION & SPORT)
            // ══════════════════════════════════════════════════
            [
                'name'         => 'Nike Official Store Indonesia',
                'slug'         => 'nike-official-store-indonesia',
                'seller_name'  => 'PT Mitra Adiperkasa Tbk',
                'seller_email' => 'seller.nike@nitipdong.com',
                'phone'        => '081298765433',
                'city'         => 'Jakarta Pusat',
                'address'      => 'Grand Indonesia Mall East Mall Lt. 3, Jakarta Pusat',
                'description'  => 'Toko resmi Nike Indonesia. Sepatu lari, sneakers ikonik, pakaian olahraga Dri-FIT, dan aksesoris original bergaransi.',
                'category_key' => 'Pakaian',
                'logo'         => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&auto=format&fit=crop&q=80',
                'banner'       => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=1200&auto=format&fit=crop&q=80',
                'products'     => [
                    [
                        'name' => 'Nike Air Max 90 Premium White Black Original',
                        'description' => 'Sneakers legendaris Nike Air Max 90 dengan bantalan Max Air empuk, upper kulit asli kombinasi mesh breathable, sol karet wafel tahan aus.',
                        'price' => 1899000,
                        'discount_percentage' => 20,
                        'rating' => 4.89,
                        'sold_count' => 3421,
                        'stock' => 60,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                        'images' => [
                            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80',
                        ],
                    ],
                    [
                        'name' => 'Nike Pegasus 40 Road Running Shoes Black White',
                        'description' => 'Sepatu lari andalan Nike Pegasus 40 dengan bantalan ganda Zoom Air di depan dan tumit, busa React super empuk untuk jarak 5K hingga Marathon.',
                        'price' => 2099000,
                        'discount_percentage' => 15,
                        'rating' => 4.93,
                        'sold_count' => 2100,
                        'stock' => 45,
                        'badge' => 'hot',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Nike Dunk Low Retro White Black Panda',
                        'description' => 'Sneaker ikonik Nike Dunk Low warna Panda White Black yang sangat populer. Kulit premium halus, desain timeless cocok untuk segala outfit.',
                        'price' => 1649000,
                        'discount_percentage' => 0,
                        'rating' => 4.96,
                        'sold_count' => 4520,
                        'stock' => 30,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Nike Sportswear Club Fleece Pullover Hoodie Black',
                        'description' => 'Hoodie fleece katun premium Nike dengan bagian dalam lembut hangat, saku kangguru besar, logo bordir Nike Futura di dada.',
                        'price' => 799000,
                        'discount_percentage' => 25,
                        'rating' => 4.82,
                        'sold_count' => 3190,
                        'stock' => 90,
                        'badge' => 'sale',
                        'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Nike Pro Dri-FIT Men Tight Training T-Shirt',
                        'description' => 'Baju kompresi olahraga pria Nike Pro dengan teknologi Dri-FIT penyerap keringat cepat, bahan elastis 4 arah nyaman untuk gym dan fitness.',
                        'price' => 459000,
                        'discount_percentage' => 10,
                        'rating' => 4.79,
                        'sold_count' => 1840,
                        'stock' => 120,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Nike Brasilia 9.5 Training Duffel Bag 60L',
                        'description' => 'Tas olahraga duffel bag Nike kapasitas 60L dengan kompartemen sepatu terpisah, saku botol minum, tali bahu empuk yang nyaman.',
                        'price' => 599000,
                        'discount_percentage' => 20,
                        'rating' => 4.87,
                        'sold_count' => 1230,
                        'stock' => 55,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════
            // 4. KOPI NUSANTARA & MAKANAN SEHAT (KULINER)
            // ══════════════════════════════════════════════════
            [
                'name'         => 'Kopi Nusantara & Makanan Sehat',
                'slug'         => 'kopi-nusantara-makanan-sehat',
                'seller_name'  => 'Budi Hartono Santoso',
                'seller_email' => 'seller.kopi@nitipdong.com',
                'phone'        => '081298765434',
                'city'         => 'Medan',
                'address'      => 'Jl. Gatot Subroto No. 88, Kota Medan, Sumatera Utara',
                'description'  => 'Distributor biji kopi specialty nusantara, madu hutan liar murni, matcha jepang asli, dan camilan sehat organik bersertifikasi BPOM.',
                'category_key' => 'Makanan',
                'logo'         => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=200&auto=format&fit=crop&q=80',
                'banner'       => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=1200&auto=format&fit=crop&q=80',
                'products'     => [
                    [
                        'name' => 'Kopi Arabica Gayo Aceh Specialty Roast 1kg',
                        'description' => 'Biji kopi arabika single origin dataran tinggi Gayo Aceh altitude 1500mdpl. Full washed process, medium roast dengan aroma dark chocolate & brown sugar.',
                        'price' => 185000,
                        'discount_percentage' => 15,
                        'rating' => 4.94,
                        'sold_count' => 3456,
                        'stock' => 150,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Madu Hutan Liar Murni Sumbawa Organik 500ml',
                        'description' => 'Madu hutan liar asli 100% dari sarang lebah Apis Dorsata Sumbawa. Tanpa pemanasan dan tanpa campuran gula, kaya antioksidan dan antibakteri.',
                        'price' => 125000,
                        'discount_percentage' => 20,
                        'rating' => 4.89,
                        'sold_count' => 2345,
                        'stock' => 80,
                        'badge' => 'hot',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Teh Hijau Matcha Ceremonial Grade A Uji Japan 100g',
                        'description' => 'Bubuk matcha murni kualitas seremonial tertinggi dari perkebunan Uji Kyoto Jepang. Warna hijau cerah dengan rasa umami manis lembut.',
                        'price' => 299000,
                        'discount_percentage' => 10,
                        'rating' => 4.93,
                        'sold_count' => 1234,
                        'stock' => 65,
                        'badge' => 'new',
                        'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Dark Chocolate 85% Cacao Single Origin Belgium 250g',
                        'description' => 'Cokelat hitam premium Belgia dengan kadar kakao 85%, rendah gula, kaya flavonoid baik untuk kesehatan jantung dan diet vegan.',
                        'price' => 159000,
                        'discount_percentage' => 30,
                        'rating' => 4.88,
                        'sold_count' => 2876,
                        'stock' => 110,
                        'badge' => 'sale',
                        'image' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Kopi Luwak Liar Asli Toraja Premium 250g',
                        'description' => 'Kopi luwak murni dari hutan Toraja pegunungan Sulawesi. Keasaman sangat rendah, aftertaste karamel yang sangat halus dan mewah.',
                        'price' => 450000,
                        'discount_percentage' => 10,
                        'rating' => 4.96,
                        'sold_count' => 480,
                        'stock' => 25,
                        'badge' => 'hot',
                        'image' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Granola Roasted Almond & Honey Healthy Breakfast 500g',
                        'description' => 'Oatmeal panggang renyah dengan kacang almond utuh, biji chia, dan madu hutan murni. Bebas minyak sawit, sarapan sehat tinggi serat.',
                        'price' => 89000,
                        'discount_percentage' => 15,
                        'rating' => 4.82,
                        'sold_count' => 1920,
                        'stock' => 140,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════
            // 5. AUTOSPEED RACING & GARAGE (OTOMOTIF)
            // ══════════════════════════════════════════════════
            [
                'name'         => 'Autospeed Racing & Garage',
                'slug'         => 'autospeed-racing-garage',
                'seller_name'  => 'Hendro Wicaksono',
                'seller_email' => 'seller.otomotif@nitipdong.com',
                'phone'        => '081298765435',
                'city'         => 'Surabaya',
                'address'      => 'Jl. Mayjen Sungkono No. 102, Kota Surabaya, Jawa Timur',
                'description'  => 'Pusat suku cadang motor & mobil, helm branded AGV / Shoei, ban Michelin resmi, oli performa tinggi, dan aksesoris racing.',
                'category_key' => 'Otomotif',
                'logo'         => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=200&auto=format&fit=crop&q=80',
                'banner'       => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=1200&auto=format&fit=crop&q=80',
                'products'     => [
                    [
                        'name' => 'Helm Full Face AGV K1 Rossi Mugello 2016 Replica',
                        'description' => 'Helm full face AGV K1 dengan motif legendaris Rossi Mugello. Shell thermoplastic berkekuatan tinggi, visor anti-gores, pengunci Double D-Ring standar balap Moto-GP.',
                        'price' => 1899000,
                        'discount_percentage' => 20,
                        'rating' => 4.92,
                        'sold_count' => 1234,
                        'stock' => 30,
                        'badge' => 'bestseller',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Ban Motor Michelin Pilot Street 2 110/70 & 140/70-17',
                        'description' => 'Paket ban tubeless Michelin Pilot Street 2 untuk motor sport 150-250cc. Alur tapak membuang air maksimal, kompon silika mencengkeram kuat di jalan basah.',
                        'price' => 1249000,
                        'discount_percentage' => 15,
                        'rating' => 4.91,
                        'sold_count' => 3456,
                        'stock' => 50,
                        'badge' => 'hot',
                        'is_featured' => true,
                        'image' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Oli Motor Shell Advance AX7 10W-40 Matic 1 Liter',
                        'description' => 'Oli semi-sintetis Shell Advance AX7 teknologi Active Cleansing menjaga kebersihan mesin motor matic, tarikan enteng dan konsumsi BBM irit.',
                        'price' => 89000,
                        'discount_percentage' => 25,
                        'rating' => 4.85,
                        'sold_count' => 8934,
                        'stock' => 300,
                        'badge' => 'sale',
                        'image' => 'https://images.unsplash.com/photo-1486006920555-c77dce18193b?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Intercom Helm Bluetooth Cardo Packtalk Bold JBL',
                        'description' => 'Intercom helm touring Cardo teknologi Dynamic Mesh Communication (DMC) jangkauan hingga 1.6km, audio speaker JBL premium, tahan air IP67.',
                        'price' => 3899000,
                        'discount_percentage' => 10,
                        'rating' => 4.97,
                        'sold_count' => 520,
                        'stock' => 18,
                        'badge' => 'new',
                        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Sarung Tangan Motor Kulit Alpinestars SP-8 V3 Leather',
                        'description' => 'Sarung tangan balap motor kulit kambing asli Alpinestars dengan pelindung knuckle polimer keras, jembatan jari anti patah, ventilasi optimal.',
                        'price' => 1450000,
                        'discount_percentage' => 15,
                        'rating' => 4.88,
                        'sold_count' => 890,
                        'stock' => 40,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800&auto=format&fit=crop&q=80',
                    ],
                    [
                        'name' => 'Box Motor Givi E43NTL Mule Top Box 43 Liter',
                        'description' => 'Box belakang motor Givi kapasitas 43L muat 2 helm full face. Kunci Monolock kokoh dan dudukan baseplate universal untuk segala jenis motor.',
                        'price' => 1199000,
                        'discount_percentage' => 10,
                        'rating' => 4.89,
                        'sold_count' => 1430,
                        'stock' => 35,
                        'badge' => null,
                        'image' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],
        ];
    }
}
