<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds for marketplace categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik & Gadget',
                'slug' => 'elektronik-gadget',
                'icon' => 'fa-solid fa-laptop',
            ],
            [
                'name' => 'Handphone & Tablet',
                'slug' => 'handphone-tablet',
                'icon' => 'fa-solid fa-mobile-screen-button',
            ],
            [
                'name' => 'Komputer & Laptop',
                'slug' => 'komputer-laptop',
                'icon' => 'fa-solid fa-computer',
            ],
            [
                'name' => 'Kamera & Audio',
                'slug' => 'kamera-audio',
                'icon' => 'fa-solid fa-camera',
            ],
            [
                'name' => 'Fashion Pria',
                'slug' => 'fashion-pria',
                'icon' => 'fa-solid fa-shirt',
            ],
            [
                'name' => 'Fashion Wanita',
                'slug' => 'fashion-wanita',
                'icon' => 'fa-solid fa-vest',
            ],
            [
                'name' => 'Sepatu & Sandal',
                'slug' => 'sepatu-sandal',
                'icon' => 'fa-solid fa-shoe-prints',
            ],
            [
                'name' => 'Tas & Aksesoris Fashion',
                'slug' => 'tas-aksesoris-fashion',
                'icon' => 'fa-solid fa-bag-shopping',
            ],
            [
                'name' => 'Jam Tangan & Kacamata',
                'slug' => 'jam-tangan-kacamata',
                'icon' => 'fa-solid fa-clock',
            ],
            [
                'name' => 'Kecantikan & Skincare',
                'slug' => 'kecantikan-skincare',
                'icon' => 'fa-solid fa-spa',
            ],
            [
                'name' => 'Kesehatan & Medis',
                'slug' => 'kesehatan-medis',
                'icon' => 'fa-solid fa-heart-pulse',
            ],
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'icon' => 'fa-solid fa-utensils',
            ],
            [
                'name' => 'Ibu & Perlengkapan Bayi',
                'slug' => 'ibu-perlengkapan-bayi',
                'icon' => 'fa-solid fa-baby',
            ],
            [
                'name' => 'Perlengkapan Rumah Tangga',
                'slug' => 'perlengkapan-rumah-tangga',
                'icon' => 'fa-solid fa-house-chimney',
            ],
            [
                'name' => 'Dapur & Ruang Makan',
                'slug' => 'dapur-ruang-makan',
                'icon' => 'fa-solid fa-kitchen-set',
            ],
            [
                'name' => 'Otomotif & Aksesoris Motor/Mobil',
                'slug' => 'otomotif-aksesoris',
                'icon' => 'fa-solid fa-car',
            ],
            [
                'name' => 'Olahraga & Aktivitas Outdoor',
                'slug' => 'olahraga-outdoor',
                'icon' => 'fa-solid fa-futbol',
            ],
            [
                'name' => 'Hobi, Mainan & Gaming',
                'slug' => 'hobi-mainan-gaming',
                'icon' => 'fa-solid fa-gamepad',
            ],
            [
                'name' => 'Buku, Alat Tulis & Kantor',
                'slug' => 'buku-alat-tulis-kantor',
                'icon' => 'fa-solid fa-book',
            ],
            [
                'name' => 'Perawatan Hewan Peliharaan',
                'slug' => 'perawatan-hewan-peliharaan',
                'icon' => 'fa-solid fa-paw',
            ],
            [
                'name' => 'Voucher & Produk Digital',
                'slug' => 'voucher-produk-digital',
                'icon' => 'fa-solid fa-ticket',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                ]
            );
        }
    }
}
