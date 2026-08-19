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
        User::create([
            'name'              => 'Super Admin Platform',
            'email'             => 'superadmin@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'super_admin',
        ]);

        // Admin
        User::create([
            'name'              => 'Admin Operasional',
            'email'             => 'admin@belanjain.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'admin',
        ]);
    }
}