<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Utama Super Admin NitipDong
        User::updateOrCreate(
            ['email' => 'sanitipdong2026@gmail.com'],
            [
                'name'              => 'Super Admin NitipDong',
                'email_verified_at' => now(),
                'password'          => Hash::make('SaNitipdong2K26*'),
                'role'              => 'super_admin',
            ]
        );

        // Kategori Marketplace
        $this->call(CategorySeeder::class);
    }
}
