<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed produk untuk setiap kategori.
     * Minimal 1–2 produk per kategori, tidak ada duplikat nama produk.
     */
    public function run(): void
    {
        // ─── 1. Pastikan ada store seller yang approved ───────────────────────
        $store = Store::where('status', 'approved')->first();

        if (! $store) {
            $seller = User::firstOrCreate(
                ['email' => 'seller@nitipdong.test'],
                [
                    'name'              => 'NitipDong Official',
                    'password'          => bcrypt('password'),
                    'role'              => 'seller',
                    'email_verified_at' => now(),
                ]
            );

            $store = Store::firstOrCreate(
                ['slug' => 'nitipdong-official'],
                [
                    'user_id'     => $seller->id,
                    'name'        => 'NitipDong Official Store',
                    'description' => 'Toko resmi NitipDong dengan produk terbaik dan terpercaya.',
                    'status'      => 'approved',
                    'city'        => 'Jakarta Pusat',
                ]
            );
        }

        // ─── 2. Daftar produk per slug kategori ──────────────────────────────
        // Gambar menggunakan Unsplash (public domain, no auth needed)
        $productsByCategory = [

            // ── Elektronik & Gadget ──────────────────────────────────────────
            'elektronik-gadget' => [
                [
                    'name'                => 'Sony WH-1000XM5 Wireless Headphone',
                    'description'         => 'Headphone over-ear premium dengan Active Noise Cancellation terbaik di kelasnya. Driver 30mm, baterai 30 jam, multi-device pairing, foldable design. Kualitas audio Hi-Res bebas gangguan.',
                    'price'               => 4999000,
                    'discount_percentage' => 15,
                    'stock'               => 42,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Anker PowerCore 26800 Power Bank',
                    'description'         => 'Power bank kapasitas besar 26800mAh, dual USB-A + USB-C output, fast charging 65W. Kompatibel dengan laptop, smartphone, dan tablet. Ideal untuk travelling dan bekerja di luar ruangan.',
                    'price'               => 699000,
                    'discount_percentage' => 20,
                    'stock'               => 120,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Handphone & Tablet ───────────────────────────────────────────
            'handphone-tablet' => [
                [
                    'name'                => 'Samsung Galaxy S25 Ultra 512GB',
                    'description'         => 'Flagship Android dengan chipset Snapdragon 8 Elite, kamera 200MP dengan zoom 100x, layar Dynamic AMOLED 2X 6.9" 120Hz. Dilengkapi S-Pen dan AI Galaxy features.',
                    'price'               => 19999000,
                    'discount_percentage' => 10,
                    'stock'               => 28,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'iPad Air M2 256GB WiFi',
                    'description'         => 'Tablet tipis dan bertenaga dengan chip M2, layar Liquid Retina 11" True Tone, Apple Pencil dan Magic Keyboard support. Cocok untuk kreator konten dan profesional.',
                    'price'               => 12999000,
                    'discount_percentage' => 5,
                    'stock'               => 35,
                    'badge'               => null,
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Xiaomi Redmi Note 14 Pro 8/256GB',
                    'description'         => 'Mid-range terbaik dengan kamera 200MP OIS, layar AMOLED 6.67" 120Hz, baterai 5000mAh dengan fast charging 67W, desain slim dan premium.',
                    'price'               => 3999000,
                    'discount_percentage' => 12,
                    'stock'               => 85,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Komputer & Laptop ────────────────────────────────────────────
            'komputer-laptop' => [
                [
                    'name'                => 'ASUS ROG Zephyrus G16 RTX 4070',
                    'description'         => 'Laptop gaming premium tipis dengan RTX 4070, AMD Ryzen 9 7945HX, RAM 32GB DDR5, SSD 1TB NVMe. Layar 16" QHD 240Hz IPS. Bodi aluminium dengan desain ROG eksklusif.',
                    'price'               => 27999000,
                    'discount_percentage' => 8,
                    'stock'               => 15,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Logitech MX Master 3S Wireless Mouse',
                    'description'         => 'Mouse wireless ergonomis dengan MagSpeed scroll wheel, 8K DPI sensor, 7 tombol programmable, USB-C charging. Koneksi Bluetooth & USB Receiver. Baterai tahan 70 hari.',
                    'price'               => 1299000,
                    'discount_percentage' => 18,
                    'stock'               => 95,
                    'badge'               => 'bestseller',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Kamera & Audio ───────────────────────────────────────────────
            'kamera-audio' => [
                [
                    'name'                => 'Sony Alpha ZV-E10 II Mirrorless Vlog Camera',
                    'description'         => 'Kamera mirrorless APS-C 26MP untuk vlogging, video 4K 60fps, layar flip touchscreen, built-in ND filter, AI subject recognition. Kit lens 16-50mm OSS.',
                    'price'               => 11500000,
                    'discount_percentage' => 10,
                    'stock'               => 22,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'JBL Xtreme 4 Portable Bluetooth Speaker',
                    'description'         => 'Speaker Bluetooth portable tahan air IP67, suara powerful 40W, baterai 24 jam, PartyBoost untuk koneksi multi speaker. Ideal untuk outdoor dan pantai.',
                    'price'               => 2899000,
                    'discount_percentage' => 22,
                    'stock'               => 50,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Fashion Pria ─────────────────────────────────────────────────
            'fashion-pria' => [
                [
                    'name'                => 'Kemeja Flannel Slim Fit Premium',
                    'description'         => 'Kemeja flannel bahan cotton twill premium, slim fit, tersedia dalam motif kotak-kotak warna netral dan bold. Cocok untuk kasual maupun semi-formal. Size S-XXL.',
                    'price'               => 349000,
                    'discount_percentage' => 30,
                    'stock'               => 200,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Celana Chino Stretch Regular Fit Pria',
                    'description'         => 'Celana chino bahan stretch yang nyaman dipakai seharian, regular fit, tersedia dalam berbagai warna netral. Anti kusut dan mudah dirawat. Cocok kasual hingga smart casual.',
                    'price'               => 299000,
                    'discount_percentage' => 25,
                    'stock'               => 175,
                    'badge'               => null,
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Fashion Wanita ───────────────────────────────────────────────
            'fashion-wanita' => [
                [
                    'name'                => 'Dress Midi Floral Chiffon Elegan',
                    'description'         => 'Dress midi motif floral bahan chiffon ringan dan breathable. Potongan A-line yang feminin dan elegan, cocok untuk kondangan, arisan, maupun dinner. Tersedia S-XL.',
                    'price'               => 279000,
                    'discount_percentage' => 35,
                    'stock'               => 150,
                    'badge'               => 'sale',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1594938298603-c8148c4b2bdd?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1594938298603-c8148c4b2bdd?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Blouse Wanita Oversize Korean Style',
                    'description'         => 'Blouse oversize style Korea, bahan linen viscose halus dan adem. Tersedia warna pastel dan earth tone. One size fit all, cocok untuk berbagai tubuh.',
                    'price'               => 189000,
                    'discount_percentage' => 20,
                    'stock'               => 300,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Sepatu & Sandal ──────────────────────────────────────────────
            'sepatu-sandal' => [
                [
                    'name'                => 'Nike Air Max 270 React Sneakers Pria',
                    'description'         => 'Sepatu lifestyle Nike Air Max 270 dengan unit React foam yang empuk dan responsif. Upper mesh breathable, outsole rubber durable. Tampilan modern dan sporty untuk sehari-hari.',
                    'price'               => 1899000,
                    'discount_percentage' => 20,
                    'stock'               => 80,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Sandal Gunung Outdoor Trekking Pro',
                    'description'         => 'Sandal outdoor bahan EVA + rubber compound, anti slip, tahan air, cocok untuk hiking dan trekking ringan. Desain ergonomis dengan tali adjustable. Size 38-44.',
                    'price'               => 399000,
                    'discount_percentage' => 15,
                    'stock'               => 120,
                    'badge'               => null,
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Tas & Aksesoris Fashion ──────────────────────────────────────
            'tas-aksesoris-fashion' => [
                [
                    'name'                => 'Tas Ransel Laptop Kulit Sintetis Premium',
                    'description'         => 'Tas ransel bahan kulit sintetis premium, kompartemen laptop 15.6", saku organizer, anti maling, USB charging port. Cocok untuk kerja, kuliah, dan travel.',
                    'price'               => 549000,
                    'discount_percentage' => 30,
                    'stock'               => 95,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Tote Bag Canvas Besar Wanita',
                    'description'         => 'Tote bag canvas tebal dan kuat, berukuran besar untuk bawa banyak barang. Desain minimalis dengan berbagai warna pilihan, ada saku dalam untuk dompet dan kunci.',
                    'price'               => 159000,
                    'discount_percentage' => 40,
                    'stock'               => 250,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Jam Tangan & Kacamata ────────────────────────────────────────
            'jam-tangan-kacamata' => [
                [
                    'name'                => 'Apple Watch Series 10 GPS 46mm',
                    'description'         => 'Smartwatch terbaru Apple dengan layar Always-On Retina lebih tipis, ECG, crash detection, blood oxygen, fitness tracking lengkap. Tali silikon sport band. Kompatibel iPhone.',
                    'price'               => 7499000,
                    'discount_percentage' => 8,
                    'stock'               => 30,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1551816230-ef5deaed4a26?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1551816230-ef5deaed4a26?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Kacamata Polarized UV400 Sporty',
                    'description'         => 'Kacamata anti silau dengan lensa polarized UV400 proteksi penuh. Frame TR90 ringan dan fleksibel, cocok untuk berkendara, olahraga outdoor, dan pantai.',
                    'price'               => 299000,
                    'discount_percentage' => 25,
                    'stock'               => 180,
                    'badge'               => null,
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Kecantikan & Skincare ────────────────────────────────────────
            'kecantikan-skincare' => [
                [
                    'name'                => 'Wardah Lightening Serum Vitamin C 20ml',
                    'description'         => 'Serum brightening dengan Vitamin C 10% dan niacinamide yang membantu mencerahkan kulit, meratakan warna kulit, dan mengurangi bekas jerawat. Cocok untuk semua jenis kulit.',
                    'price'               => 89000,
                    'discount_percentage' => 15,
                    'stock'               => 500,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Emina Sun Protection SPF 45 PA+++ 60ml',
                    'description'         => 'Sunscreen lightweight formula non-greasy SPF 45 PA+++. Melindungi kulit dari sinar UVA dan UVB, cocok untuk kulit berminyak dan kombinasi. Tanpa pewangi, bebas paraben.',
                    'price'               => 49000,
                    'discount_percentage' => 10,
                    'stock'               => 800,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Kesehatan & Medis ────────────────────────────────────────────
            'kesehatan-medis' => [
                [
                    'name'                => 'Tensimeter Digital Omron HEM-7120',
                    'description'         => 'Tensimeter digital otomatis untuk lengan atas dengan teknologi IntelliSense. Akurasi tinggi, layar LCD besar, memori 30 data, indikator detak jantung tidak teratur.',
                    'price'               => 449000,
                    'discount_percentage' => 12,
                    'stock'               => 75,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Suplemen Vitamin C 1000mg + Zinc 30 Tablet',
                    'description'         => 'Suplemen vitamin C dosis tinggi 1000mg dengan zinc dan rosehip extract untuk memperkuat imunitas, antioksidan alami, menjaga kesehatan kulit. Rasa jeruk, mudah dikonsumsi.',
                    'price'               => 79000,
                    'discount_percentage' => 20,
                    'stock'               => 600,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Makanan & Minuman ────────────────────────────────────────────
            'makanan-minuman' => [
                [
                    'name'                => 'Kopi Arabica Gayo Specialty 250g',
                    'description'         => 'Kopi single origin Arabica Gayo Aceh, biji pilihan Grade 1 specialty. Profil rasa: cokelat, karamel, sedikit floral. Tersedia roast level medium dan medium-dark. Freshly roasted.',
                    'price'               => 129000,
                    'discount_percentage' => 10,
                    'stock'               => 300,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Granola Oat Madu & Kacang 500g',
                    'description'         => 'Granola homemade dengan oat gandum utuh, madu asli, kacang almond, kismis, dan biji labu. Tanpa pengawet, tanpa pemanis buatan. Sarapan sehat dan mengenyangkan.',
                    'price'               => 89000,
                    'discount_percentage' => 15,
                    'stock'               => 200,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Ibu & Perlengkapan Bayi ──────────────────────────────────────
            'ibu-perlengkapan-bayi' => [
                [
                    'name'                => 'Stroller Bayi Lipat Ringan Travel System',
                    'description'         => 'Stroller bayi ultra ringan 5kg, bisa dilipat satu tangan, canopy besar UV protection, seat recline 165°, cocok dari newborn hingga 25kg. Frame aluminium kuat dan ringan.',
                    'price'               => 1999000,
                    'discount_percentage' => 20,
                    'stock'               => 40,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Pompa ASI Elektrik Double Hands-Free',
                    'description'         => 'Pompa ASI elektrik double, bisa dipakai hands-free tanpa kabel, 9 level hisap dan 9 level masase, mode letdown, anti balik air, mudah dibersihkan. Baterai rechargeable.',
                    'price'               => 799000,
                    'discount_percentage' => 25,
                    'stock'               => 60,
                    'badge'               => 'new',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1632408641281-0c3a7bc62849?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1632408641281-0c3a7bc62849?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Perlengkapan Rumah Tangga ────────────────────────────────────
            'perlengkapan-rumah-tangga' => [
                [
                    'name'                => 'Dyson V15 Detect Cordless Vacuum Cleaner',
                    'description'         => 'Vacuum cleaner tanpa kabel dengan laser dust detection, HEPA filter 99.97%, daya hisap 240AW, baterai 60 menit. Mendeteksi debu yang tidak terlihat mata. Berbagai attachment.',
                    'price'               => 8999000,
                    'discount_percentage' => 10,
                    'stock'               => 18,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Bantal & Guling Memory Foam Set Premium',
                    'description'         => 'Set bantal + guling memory foam premium yang menyesuaikan bentuk kepala dan leher, mengurangi tekanan, hipoalergenik, cover bamboo fiber yang sejuk dan breathable.',
                    'price'               => 399000,
                    'discount_percentage' => 35,
                    'stock'               => 200,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Dapur & Ruang Makan ──────────────────────────────────────────
            'dapur-ruang-makan' => [
                [
                    'name'                => 'Philips Air Fryer Digital XXL 7.3L',
                    'description'         => 'Air fryer XXL kapasitas 7.3L cocok untuk keluarga besar. Teknologi Rapid Air 3D circulation, 7 preset masak, twin TurboStar technology, layar digital touch. Anti lengket.',
                    'price'               => 2599000,
                    'discount_percentage' => 18,
                    'stock'               => 45,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Set Pisau Dapur Stainless 6 Pcs + Stand',
                    'description'         => 'Set pisau dapur baja stainless 5Cr15MoV profesional, anti karat, tajam tahan lama. Termasuk pisau chef, pisau roti, pisau buah, gunting dapur + stand kayu solid.',
                    'price'               => 449000,
                    'discount_percentage' => 30,
                    'stock'               => 130,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1593618998160-e34014e67546?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1593618998160-e34014e67546?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Otomotif & Aksesoris Motor/Mobil ────────────────────────────
            'otomotif-aksesoris' => [
                [
                    'name'                => 'Dashcam Mobil 4K HDR Sony STARVIS',
                    'description'         => 'Kamera dasbor mobil resolusi 4K 30fps dengan sensor Sony STARVIS, night vision excellent, wide angle 170°, GPS logger, parking mode, loop recording otomatis. Mudah dipasang.',
                    'price'               => 999000,
                    'discount_percentage' => 20,
                    'stock'               => 60,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1449130001-5be8e3e41a2c?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1449130001-5be8e3e41a2c?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Helm Full Face SNI Airoh Commander',
                    'description'         => 'Helm full face bersertifikat SNI dengan shell fiberglass ringan, visor anti-fog double, ventilasi udara optimal, inner sun visor, padding removable dan washable. Size S-XL.',
                    'price'               => 1299000,
                    'discount_percentage' => 15,
                    'stock'               => 85,
                    'badge'               => 'bestseller',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Olahraga & Aktivitas Outdoor ─────────────────────────────────
            'olahraga-outdoor' => [
                [
                    'name'                => 'Tenda Camping Ultralight 2 Orang Waterproof',
                    'description'         => 'Tenda dome 2 orang ultralight 1.8kg, bahan 210T ripstop nylon waterproof 3000mm, double layer, setup mudah, tiang fiberglass. Cocok untuk hiking, backpacking, dan camping.',
                    'price'               => 899000,
                    'discount_percentage' => 25,
                    'stock'               => 55,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Raket Badminton Yonex Astrox 88S Pro',
                    'description'         => 'Raket badminton profesional Yonex Astrox 88S Pro, frame Graphite + Tungsten, balance head heavy, cocok untuk smash keras. Sudah termasuk cover bag. Flex medium-stiff.',
                    'price'               => 2199000,
                    'discount_percentage' => 10,
                    'stock'               => 40,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Hobi, Mainan & Gaming ────────────────────────────────────────
            'hobi-mainan-gaming' => [
                [
                    'name'                => 'PlayStation 5 Slim Disc Edition',
                    'description'         => 'PS5 Slim edisi terbaru lebih tipis dan ringan, SSD 1TB, resolusi 8K, ray tracing, 3D audio. Termasuk DualSense controller wireless dan 1 game pilihan.',
                    'price'               => 8499000,
                    'discount_percentage' => 5,
                    'stock'               => 20,
                    'badge'               => 'hot',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1592840062661-a5a7f78e2056?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Lego Technic 4x4 Land Rover Defender 42110',
                    'description'         => 'Set LEGO Technic Land Rover Defender 42110 dengan 2573 pieces, suspensi independen, differential, gearbox 4 kecepatan, wiper berfungsi. Model detail untuk kolektor dan pecinta otomotif.',
                    'price'               => 1499000,
                    'discount_percentage' => 12,
                    'stock'               => 35,
                    'badge'               => 'new',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1618842676088-c4d48a6a7c9d?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1618842676088-c4d48a6a7c9d?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Buku, Alat Tulis & Kantor ────────────────────────────────────
            'buku-alat-tulis-kantor' => [
                [
                    'name'                => 'Atomic Habits - James Clear (Terjemahan)',
                    'description'         => 'Buku best seller internasional tentang membangun kebiasaan baik dan menghilangkan kebiasaan buruk. Versi terjemahan Bahasa Indonesia, hard cover, 356 halaman. Panduan perubahan hidup.',
                    'price'               => 119000,
                    'discount_percentage' => 20,
                    'stock'               => 500,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Stabilo Boss Original Highlighter 8 Warna',
                    'description'         => 'Set highlighter Stabilo Boss 8 warna: kuning, hijau, pink, biru, orange, ungu, merah, dan tosca. Tinta berbasis air, cepat kering, tidak tembus kertas. Cocok untuk pelajar dan profesional.',
                    'price'               => 79000,
                    'discount_percentage' => 10,
                    'stock'               => 800,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Perawatan Hewan Peliharaan ───────────────────────────────────
            'perawatan-hewan-peliharaan' => [
                [
                    'name'                => 'Royal Canin Adult Cat Food Indoor 4kg',
                    'description'         => 'Makanan kucing premium Royal Canin khusus indoor 4kg, formula lengkap untuk kucing dewasa indoor. Mendukung sistem pencernaan, kesehatan bulu, dan berat badan ideal.',
                    'price'               => 349000,
                    'discount_percentage' => 10,
                    'stock'               => 200,
                    'badge'               => 'bestseller',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Kandang Kucing Lipat Besi 3 Lantai',
                    'description'         => 'Kandang hewan peliharaan besi powder coat anti karat, 3 lantai dengan tangga, pintu ganda, mudah dilipat dan dipindah. Cocok untuk kucing, kelinci, dan anjing kecil.',
                    'price'               => 599000,
                    'discount_percentage' => 30,
                    'stock'               => 70,
                    'badge'               => 'sale',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1548681528-6a5c45b66063?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1548681528-6a5c45b66063?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],

            // ── Voucher & Produk Digital ─────────────────────────────────────
            'voucher-produk-digital' => [
                [
                    'name'                => 'Voucher Google Play Store Rp 100.000',
                    'description'         => 'Voucher Google Play Store senilai Rp 100.000 untuk pembelian aplikasi, game, musik, film, dan buku digital. Kode dikirim via email setelah pembayaran dikonfirmasi.',
                    'price'               => 100000,
                    'discount_percentage' => 5,
                    'stock'               => 1000,
                    'badge'               => 'hot',
                    'is_featured'         => false,
                    'image'               => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
                [
                    'name'                => 'Netflix Premium 1 Bulan 4 Layar 4K',
                    'description'         => 'Akun Netflix Premium shared 1 bulan untuk 4 layar sekaligus, kualitas video 4K Ultra HD, semua konten tersedia termasuk film dan series eksklusif. Akun aman dan terjamin aktif.',
                    'price'               => 54000,
                    'discount_percentage' => 0,
                    'stock'               => 500,
                    'badge'               => 'new',
                    'is_featured'         => true,
                    'image'               => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=800&auto=format&fit=crop&q=80',
                    'images'              => [
                        'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=800&auto=format&fit=crop&q=80',
                    ],
                ],
            ],
        ];

        // ─── 3. Seed produk per kategori ──────────────────────────────────────
        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($productsByCategory as $categorySlug => $products) {
            $category = Category::where('slug', $categorySlug)->first();

            if (! $category) {
                $this->command->warn("  ⚠️  Kategori '{$categorySlug}' tidak ditemukan, dilewati.");
                continue;
            }

            foreach ($products as $data) {
                // Cek duplikat berdasarkan nama produk (case-insensitive)
                $exists = Product::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->exists();

                if ($exists) {
                    $this->command->line("  ⏭️  Skip (sudah ada): {$data['name']}");
                    $totalSkipped++;
                    continue;
                }

                Product::create([
                    'uuid'                => (string) Str::uuid(),
                    'store_id'            => $store->id,
                    'category_id'         => $category->id,
                    'name'                => $data['name'],
                    'slug'                => Str::slug($data['name']) . '-' . Str::random(5),
                    'description'         => $data['description'],
                    'price'               => $data['price'],
                    'discount_percentage' => $data['discount_percentage'],
                    'stock'               => $data['stock'],
                    'badge'               => $data['badge'],
                    'is_featured'         => $data['is_featured'],
                    'image'               => $data['image'],
                    'images'              => $data['images'],
                    'is_active'           => true,
                ]);

                $this->command->line("  ✅ [{$category->name}] {$data['name']}");
                $totalCreated++;
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 Selesai! {$totalCreated} produk berhasil dibuat, {$totalSkipped} dilewati (duplikat).");
    }
}
