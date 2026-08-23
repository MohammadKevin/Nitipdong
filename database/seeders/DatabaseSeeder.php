<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        // User::create([
        //     'name'              => 'Super Admin Platform',
        //     'email'             => 'superadmin@belanjain.test',
        //     'email_verified_at' => now(),
        //     'password'          => Hash::make('password'),
        //     'role'              => 'super_admin',
        // ]);

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@belanjain.test'],
            [
                'name'              => 'Admin Operasional',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
            ]
        );

        // Courier Mitra NitipDong
        User::firstOrCreate(
            ['email' => 'kurir@nitipdong.com'],
            [
                'name'              => 'Mas Kevin (Kurir Mitra)',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'courier',
                'phone'             => '081234567890',
            ]
        );

        // Regional Warehouse Hubs (1 Kota 1 Gudang)
        $this->call(WarehouseSeeder::class);
    }
}
