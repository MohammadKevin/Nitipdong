<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATE ICON KATEGORI ===\n\n";

// Icon profesional dengan warna yang sesuai
$categoryIcons = [
    'elektronik'        => 'fa-solid fa-microchip text-blue-600',           // Chip/processor untuk tech
    'fashion-pria'      => 'fa-solid fa-user-tie text-slate-700',           // Formal man untuk fashion pria
    'fashion-wanita'    => 'fa-solid fa-venus text-pink-500',               // Venus symbol untuk wanita
    'makanan-minuman'   => 'fa-solid fa-utensils text-orange-500',          // Utensils untuk F&B
    'kesehatan-medis'   => 'fa-solid fa-notes-medical text-emerald-600',    // Medical notes untuk kesehatan
    'hobi-mainan'       => 'fa-solid fa-puzzle-piece text-purple-600',      // Puzzle untuk hobi
    'pakaian'           => 'fa-solid fa-shirt text-indigo-600',             // Shirt untuk pakaian umum
    'otomotif'          => 'fa-solid fa-car text-slate-800',                // Car untuk otomotif
    'olahraga'          => 'fa-solid fa-dumbbell text-cyan-600',            // Dumbbell untuk olahraga
    'rumah-tangga'      => 'fa-solid fa-house text-amber-600',              // House untuk rumah tangga
    'kecantikan'        => 'fa-solid fa-spa text-rose-400',                 // Spa untuk beauty
    'buku'              => 'fa-solid fa-book text-teal-600',                // Book untuk literatur
];

$updated = 0;

foreach ($categoryIcons as $slug => $icon) {
    $category = \App\Models\Category::where('slug', $slug)->first();

    if ($category) {
        $oldIcon = $category->icon;
        $category->update(['icon' => $icon]);

        echo "✅ {$category->name}\n";
        echo "   Old: {$oldIcon}\n";
        echo "   New: {$icon}\n\n";
        $updated++;
    }
}

echo "Total kategori diupdate: {$updated}\n";
echo "\n🎨 Icon sekarang lebih profesional dan sesuai konteks!\n";
