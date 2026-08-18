<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Super Admin Platform',
            'email'             => 'superadmin@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'super_admin',
        ]);

        User::create([
            'name'              => 'Admin Operasional',
            'email'             => 'admin@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'admin',
        ]);

        $seller = User::create([
            'name'              => 'Budi Seller',
            'email'             => 'seller@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'seller',
        ]);

        $store = Store::create([
            'user_id'     => $seller->id,
            'name'        => 'Toko Elektronik Budi',
            'slug'        => Str::slug('Toko Elektronik Budi'),
            'description' => 'Menjual aneka gadget, aksesoris komputer, dan periferal gaming original berkualitas.',
            'status'      => 'approved',
        ]);

        $categories = [
            'Elektronik'        => ['slug' => 'elektronik',        'icon' => 'fa-solid fa-laptop text-blue-600'],
            'Fashion Pria'      => ['slug' => 'fashion-pria',      'icon' => 'fa-solid fa-shirt text-indigo-600'],
            'Fashion Wanita'    => ['slug' => 'fashion-wanita',    'icon' => 'fa-solid fa-wand-magic-sparkles text-pink-600'],
            'Makanan & Minuman' => ['slug' => 'makanan-minuman',   'icon' => 'fa-solid fa-burger text-amber-600'],
            'Kesehatan & Medis' => ['slug' => 'kesehatan-medis',   'icon' => 'fa-solid fa-heart-pulse text-rose-600'],
            'Hobi & Mainan'     => ['slug' => 'hobi-mainan',       'icon' => 'fa-solid fa-gamepad text-emerald-600'],
        ];

        $catModels = [];
        foreach ($categories as $name => $data) {
            $catModels[$data['slug']] = Category::create([
                'name' => $name,
                'slug' => $data['slug'],
                'icon' => $data['icon'],
            ]);
        }

        $dummyProducts = [
            [
                'name'                => 'Keyboard Mechanical RGB Wireless 75%',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Keyboard mechanical premium dengan switch red linear, konektivitas Bluetooth 5.0 / 2.4Ghz / Type-C. Baterai tahan 200 jam.',
                'price'               => 450000,
                'discount_percentage' => 30,
                'stock'               => 25,
            ],
            [
                'name'                => 'Mouse Gaming Wireless Ultra-Lightweight 65g',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Sensor optik 26.000 DPI PAW3395, polling rate 1000Hz, PTFE skates presisi tinggi.',
                'price'               => 280000,
                'discount_percentage' => 20,
                'stock'               => 40,
            ],
            [
                'name'                => 'Headset Gaming 7.1 Surround Sound Noise Cancelling',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Audio spasial 7.1 dengan driver Neodymium 50mm, mikrofon detachable clear cast.',
                'price'               => 399000,
                'discount_percentage' => 25,
                'stock'               => 15,
            ],
            [
                'name'                => 'Monitor Gaming 24 Inch IPS 180Hz 1ms',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Resolusi FHD 1920x1080, sRGB 100%, AMD FreeSync Premium, bezel ultra tipis.',
                'price'               => 1650000,
                'discount_percentage' => 15,
                'stock'               => 8,
            ],
            [
                'name'                => 'Smartwatch Fitness Tracker Waterproof IP68',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Monitor detak jantung 24/7, SpO2, sleep tracking, 100+ mode olahraga, layar AMOLED 1.43".',
                'price'               => 520000,
                'discount_percentage' => 40,
                'stock'               => 30,
            ],
            [
                'name'                => 'Webcam FHD 1080P 60FPS with Ring Light',
                'category_id'         => $catModels['elektronik']->id,
                'description'         => 'Autofokus cepat, microphone stereo ganda dengan peredam bising, ring light 3 tingkat kecerahan.',
                'price'               => 320000,
                'discount_percentage' => 0,
                'stock'               => 18,
            ],
        ];

        $createdProducts = [];
        foreach ($dummyProducts as $dp) {
            $createdProducts[] = Product::create([
                'store_id'            => $store->id,
                'category_id'         => $dp['category_id'],
                'name'                => $dp['name'],
                'slug'                => Str::slug($dp['name']) . '-' . Str::random(5),
                'description'         => $dp['description'],
                'price'               => $dp['price'],
                'discount_percentage' => $dp['discount_percentage'],
                'stock'               => $dp['stock'],
                'is_active'           => true,
            ]);
        }

        $flashSale = FlashSale::create([
            'title'      => 'Flash Sale Spesial Brand Day',
            'start_time' => now()->subMinutes(15),
            'end_time'   => now()->addHours(6),
            'is_active'  => true,
        ]);

        if (count($createdProducts) >= 3) {
            FlashSaleItem::create([
                'flash_sale_id'       => $flashSale->id,
                'product_id'          => $createdProducts[0]->id,
                'flash_sale_price'    => 299000,
                'discount_percentage' => 34,
                'stock_allocated'     => 15,
                'stock_sold'          => 6,
                'is_active'           => true,
            ]);

            FlashSaleItem::create([
                'flash_sale_id'       => $flashSale->id,
                'product_id'          => $createdProducts[1]->id,
                'flash_sale_price'    => 199000,
                'discount_percentage' => 29,
                'stock_allocated'     => 20,
                'stock_sold'          => 14,
                'is_active'           => true,
            ]);

            FlashSaleItem::create([
                'flash_sale_id'       => $flashSale->id,
                'product_id'          => $createdProducts[2]->id,
                'flash_sale_price'    => 279000,
                'discount_percentage' => 30,
                'stock_allocated'     => 10,
                'stock_sold'          => 4,
                'is_active'           => true,
            ]);
        }

        $vouchers = [
            [
                'store_id'     => null,
                'code'         => 'BELANJAIN10',
                'name'         => 'Diskon 10% BelanjaIn',
                'description'  => 'Potongan 10% untuk semua transaksi di platform BelanjaIn minimal belanja Rp50.000.',
                'type'         => 'percent',
                'amount'       => 10,
                'min_spend'    => 50000,
                'max_discount' => 25000,
                'quota'        => 500,
                'is_active'    => true,
            ],
            [
                'store_id'     => null,
                'code'         => 'HEMAT50',
                'name'         => 'Potongan Rp50.000 BelanjaIn',
                'description'  => 'Potongan langsung Rp50.000 dari BelanjaIn untuk pembelian minimal Rp150.000.',
                'type'         => 'fixed',
                'amount'       => 50000,
                'min_spend'    => 150000,
                'max_discount' => null,
                'quota'        => 200,
                'is_active'    => true,
            ],
            [
                'store_id'     => null,
                'code'         => 'GRATISONGKIR',
                'name'         => 'Potongan Ongkir Rp15.000 BelanjaIn',
                'description'  => 'Kupon potongan ongkir Rp15.000 dari BelanjaIn dengan minimal belanja Rp30.000.',
                'type'         => 'fixed',
                'amount'       => 15000,
                'min_spend'    => 30000,
                'max_discount' => null,
                'quota'        => 1000,
                'is_active'    => true,
            ],
            [
                'store_id'     => $store->id,
                'code'         => 'BUDITECH15',
                'name'         => 'Diskon 15% Toko Budi',
                'description'  => 'Voucher promosi resmi dari Toko Elektronik Budi diskon 15% maks. Rp45.000.',
                'type'         => 'percent',
                'amount'       => 15,
                'min_spend'    => 75000,
                'max_discount' => 45000,
                'quota'        => 150,
                'is_active'    => true,
            ],
            [
                'store_id'     => $store->id,
                'code'         => 'BUDIGADGET',
                'name'         => 'Potongan Rp25.000 Toko Budi',
                'description'  => 'Potongan Rp25.000 khusus produk di Toko Elektronik Budi minimal pembelian Rp80.000.',
                'type'         => 'fixed',
                'amount'       => 25000,
                'min_spend'    => 80000,
                'max_discount' => null,
                'quota'        => 100,
                'is_active'    => true,
            ],
        ];

        foreach ($vouchers as $vc) {
            Voucher::create($vc);
        }

        User::create([
            'name'              => 'Siti Customer',
            'email'             => 'customer@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'customer',
        ]);
    }
}