<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sanitipdong2026@gmail.com'],
            [
                'name'              => 'Super Admin NitipDong',
                'email_verified_at' => now(),
                'password'          => Hash::make('SaNitipdong2K26*'),
                'role'              => 'super_admin',
            ]
        );
    }
}
