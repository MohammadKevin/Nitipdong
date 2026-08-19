<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TAMBAH KEYBOARD REXUS DAXA M84X ===\n\n";

$store = \App\Models\Store::where('status', 'approved')->first();
$category = \App\Models\Category::where('slug', 'elektronik')->first();

if (!$store || !$category) {
    echo "❌ Store atau kategori tidak ditemukan!\n";
    exit(1);
}

// Cek apakah foto ada
$photoPath = 'img/rexus-daxa-m84x.jpg';
if (!file_exists(public_path($photoPath))) {
    echo "⚠️  WARNING: Foto belum ada di public/img/rexus-daxa-m84x.jpg\n";
    echo "   Produk akan dibuat tanpa foto dulu.\n";
    $photoPath = null;
}

$keyboard = \App\Models\Product::create([
    'store_id'            => $store->id,
    'category_id'         => $category->id,
    'name'                => 'Rexus Keyboard Gaming Mechanical Daxa M84X Ultimate RGB Hot-Swappable',
    'slug'                => \Illuminate\Support\Str::slug('Rexus Keyboard Gaming Mechanical Daxa M84X Ultimate RGB') . '-' . \Illuminate\Support\Str::random(5),
    'description'         => "Rexus Daxa M84X Ultimate - Keyboard Gaming Mechanical RGB Premium dengan Hot-Swappable Switch

⌨️ KEYBOARD GAMING MECHANICAL PROFESIONAL DENGAN FITUR ULTIMATE

Rexus Daxa M84X Ultimate adalah keyboard gaming mechanical premium dengan layout 75% compact, dilengkapi teknologi hot-swappable switch, RGB lighting full spektrum, dan build quality premium. Dirancang untuk gamers dan content creator yang menginginkan performa maksimal dengan desain modern.

📊 SPESIFIKASI UTAMA:
• Layout: 75% Compact (84 Keys) - Space Saving Design
• Switch: Hot-Swappable Mechanical Switch (Bisa ganti switch tanpa solder)
• Switch Type: Red Linear (Default) - Smooth & Silent
• Actuation Force: 45g ± 5g
• Total Travel: 4.0mm
• Pre-Travel: 2.0mm ± 0.5mm
• Durability: 50 Million Keystrokes
• Anti-Ghosting: Full N-Key Rollover (NKRO)
• Response Time: <1ms

🎨 DESIGN & BUILD QUALITY:
• Material: Aluminum Top Plate + ABS High-Grade Body
• Keycaps: Double-Shot PBT (Anti-Shine, Tahan Lama)
• Keycap Profile: OEM Profile (Ergonomic)
• Cable: Detachable USB-C Braided Cable (1.8m)
• Feet: Dual-Stage Adjustable (2 tinggi kemiringan)
• Weight: 850g (Solid & Premium Feel)
• Dimension: 318mm x 138mm x 38mm

💡 RGB LIGHTING:
• RGB Backlit: Per-Key RGB dengan 16.8 Juta Warna
• Lighting Modes: 18+ Preset Efek RGB
• Brightness Level: 5 Tingkat Kecerahan
• Software Control: Rexus Gaming Software (Windows/Mac)
• Custom RGB: Buat efek lighting sendiri via software

🔧 HOT-SWAPPABLE FEATURE:
• Ganti Switch Tanpa Solder - Upgrade switch kapan saja
• Compatible: Semua 3-Pin & 5-Pin Mechanical Switch
• Switch Puller: Included di dalam box
• Customization: Mix & match switch sesuai preferensi
• Future Proof: Upgrade switch baru tanpa beli keyboard baru

⚡ CONNECTIVITY & COMPATIBILITY:
• Connection: USB Type-C Wired (Detachable Cable)
• Polling Rate: 1000Hz (1ms Response)
• Compatibility: Windows 7/8/10/11, MacOS, Linux
• Plug & Play: Langsung pakai tanpa driver
• Software: Optional (untuk RGB & macro programming)

🎮 GAMING FEATURES:
• Game Mode: Windows Key Lock (Fn + Win)
• Macro Support: Programmable via Rexus Software
• Media Controls: Dedicated Fn Shortcut
• Profile Memory: On-Board Memory untuk simpan setting
• Compact Layout: Lebih banyak space untuk mouse movement

📦 KELENGKAPAN BOX:
✅ Rexus Daxa M84X Ultimate Keyboard
✅ Detachable USB-C Braided Cable (1.8m)
✅ Switch Puller Tool
✅ Keycap Puller Tool
✅ Extra Keycaps (WASD + Arrow Keys Gaming Style)
✅ User Manual Indonesia/English
✅ Warranty Card

🎯 PERFECT FOR:
• Gaming: FPS, MOBA, MMO, Battle Royale
• Content Creation: Editing Video, Streaming
• Programming & Coding
• Office Work & Productivity
• Mechanical Keyboard Enthusiast

🏆 KEUNGGULAN DAXA M84X:
• Hot-Swappable - Upgrade switch tanpa solder
• 75% Compact - Hemat space, tetap lengkap
• PBT Keycaps - Tahan lama, tidak mengkilap
• Aluminum Plate - Premium & kokoh
• Type-C Detachable - Mudah dibawa traveling
• Per-Key RGB - Customization penuh

🛡️ GARANSI & SUPPORT:
• Garansi Resmi Rexus Indonesia 1 Tahun
• After Sales Service Rexus Service Center
• Free Technical Support
• Spare Parts Available

🔥 BONUS EXCLUSIVE:
• Extra Keycaps Gaming Style (WASD + Arrow)
• Switch & Keycap Puller Tools
• Premium Braided Cable USB-C
• Rexus Software License

💡 TIPS KUSTOMISASI:
Keyboard ini support semua mechanical switch 3-pin & 5-pin:
• Gaming: Red Linear, Silver Speed
• Typing: Brown Tactile, Blue Clicky
• Hybrid: Yellow Linear, Orange Tactile

⚠️ STOK TERBATAS!
📌 100% Original Rexus Indonesia
🚚 Packing Aman dengan Bubble Wrap Berlapis
🎁 Bonus Extra Keycaps Gaming Style",
    'price'               => 599000, // Harga dasar seller
    'discount_percentage' => 10,
    'stock'               => 25,
    'image'               => $photoPath,
    'is_active'           => true,
]);

echo "✅ Keyboard Rexus Daxa M84X berhasil ditambahkan!\n";
echo "   ID: {$keyboard->id}\n";
echo "   Nama: {$keyboard->name}\n";
echo "   Harga seller: Rp " . number_format($keyboard->price, 0, ',', '.') . "\n";
echo "   Harga customer: Rp " . number_format($keyboard->final_price, 0, ',', '.') . "\n";
echo "   Diskon: {$keyboard->discount_percentage}%\n";
echo "   Stok: {$keyboard->stock} unit\n";
echo "   Foto: " . ($keyboard->image ?? 'Belum ada') . "\n\n";

if (!$photoPath) {
    echo "📸 CATATAN: Simpan foto keyboard sebagai 'public/img/rexus-daxa-m84x.jpg'\n";
    echo "   Lalu jalankan: php artisan cache:clear\n";
}

echo "\n🎉 SELESAI!\n";
