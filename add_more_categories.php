<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TAMBAH KATEGORI LENGKAP ===\n\n";

$newCategories = [
    'Fashion Pria' => [
        'slug' => 'fashion-pria',
        'icon' => 'fa-solid fa-user-tie text-slate-700',
    ],
    'Fashion Wanita' => [
        'slug' => 'fashion-wanita',
        'icon' => 'fa-solid fa-venus text-pink-500',
    ],
    'Makanan & Minuman' => [
        'slug' => 'makanan-minuman',
        'icon' => 'fa-solid fa-utensils text-orange-500',
    ],
    'Kesehatan & Medis' => [
        'slug' => 'kesehatan-medis',
        'icon' => 'fa-solid fa-notes-medical text-emerald-600',
    ],
    'Hobi & Mainan' => [
        'slug' => 'hobi-mainan',
        'icon' => 'fa-solid fa-puzzle-piece text-purple-600',
    ],
    'Olahraga & Fitness' => [
        'slug' => 'olahraga-fitness',
        'icon' => 'fa-solid fa-dumbbell text-cyan-600',
    ],
    'Rumah Tangga' => [
        'slug' => 'rumah-tangga',
        'icon' => 'fa-solid fa-house text-amber-600',
    ],
    'Kecantikan' => [
        'slug' => 'kecantikan',
        'icon' => 'fa-solid fa-spa text-rose-400',
    ],
    'Buku & Alat Tulis' => [
        'slug' => 'buku-alat-tulis',
        'icon' => 'fa-solid fa-book text-teal-600',
    ],
    'Perlengkapan Bayi' => [
        'slug' => 'perlengkapan-bayi',
        'icon' => 'fa-solid fa-baby-carriage text-sky-400',
    ],
];

foreach ($newCategories as $name => $data) {
    $existing = \App\Models\Category::where('slug', $data['slug'])->first();

    if ($existing) {
        echo "⚠️  {$name} sudah ada, skip...\n";
    } else {
        \App\Models\Category::create([
            'name' => $name,
            'slug' => $data['slug'],
            'icon' => $data['icon'],
        ]);
        echo "✅ {$name} ditambahkan\n";
    }
}

echo "\n🎉 Kategori sudah lengkap dengan icon profesional!\n";
echo "Total kategori: " . \App\Models\Category::count() . "\n";
