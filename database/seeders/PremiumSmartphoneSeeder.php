<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PremiumSmartphoneSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kategori Elektronik
        $category = Category::where('slug', 'elektronik')->first();

        if (!$category) {
            $this->command->error('Kategori Elektronik tidak ditemukan! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // Ambil store pertama yang sudah approved
        $store = Store::where('status', 'approved')->first();

        if (!$store) {
            $this->command->error('Belum ada toko yang approved! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // iPhone 15 Pro Max
        $iphone = Product::create([
            'store_id'            => $store->id,
            'category_id'         => $category->id,
            'name'                => 'iPhone 15 Pro Max 256GB Natural Titanium',
            'slug'                => Str::slug('iPhone 15 Pro Max 256GB Natural Titanium') . '-' . Str::random(5),
            'description'         => "iPhone 15 Pro Max — Smartphone flagship Apple dengan chip A17 Pro 3nm, layar Super Retina XDR 6.7 inci dengan ProMotion 120Hz, dan frame titanium premium.

📱 SPESIFIKASI UTAMA:
• Chip: A17 Pro (3nm) - Performa tercepat di smartphone
• Layar: 6.7\" Super Retina XDR OLED, 2796 x 1290 piksel, ProMotion 120Hz
• Kamera: Triple 48MP Main + 12MP Ultra Wide + 12MP Telephoto 5x Optical Zoom
• Video: 4K ProRes, Spatial Video untuk Apple Vision Pro
• Material: Titanium Grade 5 (Ringan & Kokoh)
• Baterai: Hingga 29 jam video playback
• Port: USB-C dengan USB 3.0 (transfer 10Gbps)
• Action Button: Tombol khusus yang bisa dikustomisasi
• Dynamic Island: UI interaktif untuk notifikasi

🎨 WARNA: Natural Titanium (Abu-abu elegan)
💾 KAPASITAS: 256GB (Ideal untuk foto & video 4K)

📦 KELENGKAPAN BOX:
✅ iPhone 15 Pro Max
✅ Kabel USB-C to USB-C
✅ Dokumentasi
✅ SIM Ejector Tool
✅ Garansi Resmi Apple Indonesia 1 Tahun

🛡️ GARANSI & LAYANAN:
• Garansi Resmi Apple Indonesia 1 Tahun
• Bergaransi iBox/Erajaya/TAM (Pilihan sesuai stok)
• After Sales Service di seluruh Apple Authorized Service Provider Indonesia
• Eligible untuk AppleCare+ (Opsional)

🔒 KONDISI: BNIB (Brand New In Box) - Segel Resmi
✨ 100% Original Apple Indonesia
📱 Dual SIM (nano-SIM + eSIM)
🌐 Support 5G All Operator Indonesia

⚡ FREE BONUS:
• Tempered Glass Premium
• Clear Case Original
• Cable Protector",
            'price'               => 18999000, // Harga dasar seller (akan ditampilkan ke customer dengan markup 5%)
            'discount_percentage' => 5,
            'stock'               => 12,
            'is_active'           => true,
        ]);

        $this->command->info("✅ Produk '{$iphone->name}' berhasil ditambahkan (ID: {$iphone->id})");

        // Samsung Galaxy S24 Ultra
        $samsung = Product::create([
            'store_id'            => $store->id,
            'category_id'         => $category->id,
            'name'                => 'Samsung Galaxy S24 Ultra 12/256GB Titanium Gray',
            'slug'                => Str::slug('Samsung Galaxy S24 Ultra 12/256GB Titanium Gray') . '-' . Str::random(5),
            'description'         => "Samsung Galaxy S24 Ultra — Smartphone flagship Samsung dengan chip Snapdragon 8 Gen 3 for Galaxy, layar Dynamic AMOLED 2X 6.8 inci, S Pen terintegrasi, dan kamera 200MP.

📱 SPESIFIKASI UTAMA:
• Prosesor: Snapdragon 8 Gen 3 for Galaxy (4nm) - Performa maksimal gaming & AI
• RAM: 12GB LPDDR5X
• Storage: 256GB UFS 4.0 (Super cepat)
• Layar: 6.8\" Dynamic AMOLED 2X, QHD+ (3120 x 1440), 120Hz Adaptive
• Kamera Belakang:
  - 200MP Main Wide (f/1.7, OIS, Laser AF)
  - 50MP Periscope Telephoto 5x Optical (f/3.4, OIS)
  - 10MP Telephoto 3x Optical (f/2.4, OIS)
  - 12MP Ultra Wide (f/2.2, 120°)
• Kamera Depan: 12MP (f/2.2)
• Video: 8K @ 30fps, 4K @ 60fps dengan Super Steady OIS
• Baterai: 5000mAh dengan Fast Charging 45W & Wireless Charging 15W
• S Pen: Integrated stylus dengan Air Actions & low latency 2.8ms
• Material: Titanium Frame dengan Corning Gorilla Armor (Anti-reflective)

🤖 GALAXY AI FEATURES:
• Circle to Search with Google
• Live Translate (Panggilan real-time)
• Note Assist & Transcript Assist
• Photo Assist dengan Generative Edit
• Chat Assist untuk tone & grammar

🎨 WARNA: Titanium Gray (Premium & Modern)
💾 VARIAN: 12GB RAM + 256GB Storage

📦 KELENGKAPAN BOX:
✅ Samsung Galaxy S24 Ultra
✅ S Pen (Terintegrasi di body)
✅ Kabel USB-C to USB-C
✅ SIM Ejector Tool
✅ Quick Start Guide
✅ Garansi Resmi SEIN 1 Tahun

🛡️ GARANSI & LAYANAN:
• Garansi Resmi Samsung Indonesia (SEIN) 1 Tahun
• After Sales Service di Samsung Service Center seluruh Indonesia
• Premium Care Support
• Samsung Members Priority Support

🔒 KONDISI: BNIB (Brand New In Box) - Segel Resmi
✨ 100% Original Samsung Indonesia (SEIN)
📱 Dual SIM (nano-SIM + eSIM)
🌐 Support 5G All Operator Indonesia
💧 IP68 Water & Dust Resistant

🎁 FREE BONUS:
• Standing Clear Case Original Samsung
• Tempered Glass UV Premium
• Samsung Care+ Trial 1 Bulan
• Galaxy Buds FE (Bonus terbatas, selama stok tersedia)

⚡ SPESIAL PROMO: Diskon 8% + Cashback hingga Rp500.000 untuk pembayaran tertentu",
            'price'               => 16499000, // Harga dasar seller
            'discount_percentage' => 8,
            'stock'               => 15,
            'is_active'           => true,
        ]);

        $this->command->info("✅ Produk '{$samsung->name}' berhasil ditambahkan (ID: {$samsung->id})");
        $this->command->info("\n🎉 Total 2 produk smartphone premium berhasil ditambahkan ke toko '{$store->name}'");
    }
}
