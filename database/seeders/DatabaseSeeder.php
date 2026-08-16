<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'Super Admin Platform',
            'email' => 'superadmin@belanjain.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // 2. Admin (Asisten / Moderator)
        User::create([
            'name' => 'Admin Operasional',
            'email' => 'admin@belanjain.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Seller (Penjual) + Pembuatan Toko
        $seller = User::create([
            'name' => 'Budi Seller',
            'email' => 'seller@belanjain.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Toko Elektronik Budi',
            'slug' => Str::slug('Toko Elektronik Budi'),
            'description' => 'Menjual aneka gadget dan aksesoris komputer.',
            'status' => 'approved',
        ]);

        // Kategori dummy
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        // Produk dummy
        Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Keyboard Mechanical RGB',
            'slug' => Str::slug('Keyboard Mechanical RGB'),
            'description' => 'Keyboard switch mechanical red switch.',
            'price' => 350000,
            'stock' => 20,
            'is_active' => true,
        ]);

        // 4. Customer (Pembeli)
        User::create([
            'name' => 'Siti Customer',
            'email' => 'customer@belanjain.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}