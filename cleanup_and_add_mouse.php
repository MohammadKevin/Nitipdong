<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CLEANUP & ADD MOUSE PULSAR X2 ===\n\n";

// 1. Hapus semua produk yang tidak punya foto
$productsWithoutImage = \App\Models\Product::whereNull('image')
    ->orWhere('image', '')
    ->get();

echo "Produk tanpa foto yang akan dihapus:\n";
foreach ($productsWithoutImage as $p) {
    echo "  - ID {$p->id}: {$p->name}\n";
    $p->delete();
}
echo "Total dihapus: " . $productsWithoutImage->count() . " produk\n\n";

// 2. Ambil store
$store = \App\Models\Store::where('status', 'approved')->first();
$category = \App\Models\Category::where('slug', 'elektronik')->first();

if (!$store || !$category) {
    echo "❌ Store atau kategori tidak ditemukan!\n";
    exit(1);
}

// 3. Tambah Mouse Pulsar X2 Susanto
$mouse = \App\Models\Product::create([
    'store_id'            => $store->id,
    'category_id'         => $category->id,
    'name'                => 'Pulsar X2 Gaming Mouse Wireless - Susanto Limited Edition',
    'slug'                => \Illuminate\Support\Str::slug('Pulsar X2 Gaming Mouse Wireless Susanto Limited Edition') . '-' . \Illuminate\Support\Str::random(5),
    'description'         => "Pulsar X2 Gaming Mouse Wireless - Edisi Spesial Susanto Limited Edition

🖱️ MOUSE GAMING ULTRA-LIGHTWEIGHT DENGAN PERFORMA PROFESIONAL

Pulsar X2 adalah gaming mouse wireless premium dengan bobot ultra-ringan hanya 59 gram, dirancang khusus untuk gamers kompetitif dan esports enthusiast. Edisi Susanto ini menampilkan desain eksklusif dengan colorway khusus yang tidak dijual umum.

📊 SPESIFIKASI TEKNIS:
• Sensor: PixArt PAW3395 Custom - Optical Sensor Flagship
• DPI: 100 - 26,000 (adjustable 50 DPI steps)
• Max Tracking Speed: 650 IPS
• Max Acceleration: 50G
• Polling Rate: 1000Hz (wired & wireless)
• Response Time: <1ms ultra-low latency
• Switches: Kalih GM 8.0 (80 juta klik)
• Battery Life: Hingga 70 jam wireless (RGB off)

🎨 DESIGN & BUILD:
• Berat: 59 gram (tanpa kabel)
• Shape: Medium Ergo - Cocok untuk Claw & Palm Grip
• Material: ABS High-Grade dengan Matte Coating Anti-Fingerprint
• Colorway: Susanto Edition - Black & Gold Accent (Eksklusif)
• RGB Lighting: Pulsar Logo RGB dengan 16.8 juta warna
• Glides: 100% Virgin Grade PTFE Skates (super smooth)

🔋 KONEKTIVITAS:
• Dual Mode: 2.4Ghz Wireless + USB Type-C Wired
• USB Dongle 2.4Ghz: Ultra-low latency wireless
• Paracord Cable: USB-C flexible cable (1.8m)
• Fast Charging: 10 menit charge = 7 jam gaming

⚡ FITUR GAMING:
• On-Board Memory: Simpan 5 profil DPI di mouse
• Pulsar Software: Macro, DPI adjustment, debounce time, RGB control
• LOD (Lift-Off Distance): 1-2mm adjustable
• Debounce Time: 2ms-16ms adjustable (eliminasi double click)
• DPI Indicator: LED indicator untuk setiap profil DPI

📦 KELENGKAPAN BOX:
✅ Pulsar X2 Gaming Mouse - Susanto Limited Edition
✅ USB 2.4Ghz Wireless Dongle
✅ Paracord USB-C Cable (1.8m)
✅ USB Dongle Extension Adapter
✅ Extra PTFE Mouse Skates (1 set)
✅ Anti-Slip Grip Tape
✅ User Manual & Warranty Card

🎯 PERFECT FOR:
• FPS Games: Valorant, CS2, Apex Legends, Overwatch
• MOBA: Dota 2, League of Legends
• Battle Royale: PUBG, Fortnite, Warzone
• Content Creation & Productivity

🏆 KEUNGGULAN PULSAR X2:
• 59g Ultra-Lightweight - Kontrol presisi tanpa lelah
• PAW3395 Sensor - Akurasi pixel-perfect tracking
• Kalih GM 8.0 Switches - Klik responsif dan tahan lama
• 70 Jam Battery - Gaming marathon tanpa charging
• Susanto Edition - Colorway eksklusif limited stock

🛡️ GARANSI & KUALITAS:
• Garansi Resmi Distributor Indonesia 1 Tahun
• Anti-Slip Grip Tape & Extra Skates
• Professional Gaming Grade Quality
• Used by Pro Players & Streamers Worldwide

🔥 EDISI TERBATAS SUSANTO:
• Limited Production - Hanya 500 unit di Indonesia
• Eksklusif Black & Gold Design
• Sertifikat Keaslian Numbered Edition
• Premium Packaging Box

⚠️ STOK TERBATAS! First come first served!
📌 100% Original Pulsar Gaming
🚚 Garansi Pengiriman Aman dengan Bubble Wrap Tebal",
    'price'               => 650000, // Harga dasar seller
    'discount_percentage' => 15,
    'stock'               => 20,
    'image'               => 'img/pulsar-x-susanto.jpg',
    'is_active'           => true,
]);

echo "✅ Mouse Pulsar X2 Susanto berhasil ditambahkan!\n";
echo "   ID: {$mouse->id}\n";
echo "   Nama: {$mouse->name}\n";
echo "   Harga seller: Rp " . number_format($mouse->price, 0, ',', '.') . "\n";
echo "   Diskon: {$mouse->discount_percentage}%\n";
echo "   Stok: {$mouse->stock} unit\n";
echo "   Foto: {$mouse->image}\n\n";

echo "🎉 SELESAI!\n";
echo "Total produk aktif sekarang: " . \App\Models\Product::count() . "\n";
