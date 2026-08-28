<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleSellerAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds (Khusus Environment Local).
     */
    public function run(): void
    {
        // 1. Buat Akun Seller Sample
        $sellerUser = User::updateOrCreate(
            ['email' => 'seller.demo@nitipdong.com'],
            [
                'name'              => 'Mitra Official NitipDong',
                'email_verified_at' => now(),
                'password'          => Hash::make('Password123*'),
                'role'              => 'seller',
                'phone'             => '081298765432',
                'is_banned'         => false,
            ]
        );

        // 2. Buat Toko Resmi (Approved Store)
        $store = Store::firstOrNew(['slug' => 'nitipdong-official-store']);
        $store->fill([
            'user_id'             => $sellerUser->id,
            'name'                => 'NitipDong Official Store',
            'slug'                => 'nitipdong-official-store',
            'description'         => 'Toko Resmi Terverifikasi NitipDong Indonesia. Menyediakan aneka gadget, audio premium, dan produk teknologi terjamin 100% original bergaransi resmi.',
            'address'             => 'Jl. Medan Merdeka Barat No. 12, Gambir',
            'province'            => 'DKI Jakarta',
            'city'                => 'Jakarta Pusat',
            'district'            => 'Gambir',
            'postal_code'         => '10110',
            'status'              => 'approved',
            'balance'             => 2500000,
            'bank_name'           => 'BCA',
            'bank_account_number' => '8881234567',
            'bank_account_holder' => 'NitipDong Official Store',
        ]);
        $store->save();

        // 3. Pastikan Kategori Tersedia
        $category = Category::firstOrCreate(
            ['slug' => 'elektronik-gadget'],
            [
                'name' => 'Elektronik & Gadget',
                'icon' => 'fa-solid fa-laptop',
            ]
        );

        // 4. Buat 1 Sample Produk Unggulan
        Product::updateOrCreate(
            [
                'store_id' => $store->id,
                'slug'     => 'sony-wh-1000xm5-wireless-noise-cancelling-headphones',
            ],
            [
                'uuid'                => (string) Str::uuid(),
                'category_id'         => $category->id,
                'name'                => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones - Original Garansi Resmi',
                'description'         => "Sony WH-1000XM5 menghadirkan pengalaman audio premium dengan teknologi Noise Cancelling terdepan di industri. Dilengkapi dengan 8 mikrofon dan dua prosesor terintegrasi untuk kejernihan suara luar biasa.\n\nFitur Utama:\n• Industry-leading noise cancellation dengan 2 prosesor & 8 mikrofon\n• Kualitas suara istimewa dengan driver 30mm yang dirancang khusus\n• Panggilan hands-free sebening kristal dengan 4 mikrofon beamforming\n• Daya tahan baterai hingga 30 jam dengan pengisian cepat (3 menit untuk 3 jam playback)\n• Desain ultra-nyaman dan ringan dengan material soft fit leather.",
                'price'               => 5999000,
                'discount_percentage' => 15,
                'stock'               => 25,
                'weight'              => 0.85,
                'condition'           => 'new',
                'image'               => 'img/macbookm3pro.jpg',
                'images'              => [
                    'img/macbookm3pro.jpg',
                    'img/logitech.webp',
                    'img/ps5resmi.webp',
                ],
                'specifications'      => [
                    'Merek'         => 'Sony',
                    'Tipe Koneksi'  => 'Bluetooth 5.2 / Jack 3.5mm',
                    'Daya Baterai'  => 'Hingga 30 Jam (ANC On)',
                    'Garansi Resmi' => '1 Tahun Sony Indonesia',
                ],
                'variants'            => [
                    ['name' => 'Warna', 'options' => ['Black Titanium', 'Silver Platinum', 'Midnight Blue']],
                ],
                'is_featured'         => true,
                'badge'               => 'Official Store',
                'rating'              => 4.9,
                'sold_count'          => 18,
                'is_active'           => true,
            ]
        );

        $this->command?->info('Sample Seller dan 1 Produk Local berhasil di-seed!');
    }
}
