<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'code'      => 'DC-SBY-01',
                'name'      => 'NitipDong Hub DC Surabaya',
                'city'      => 'Surabaya',
                'province'  => 'Jawa Timur',
                'address'   => 'Kawasan Industri SIER, Jl. Rungkut Industri Raya No. 42, Rungkut, Surabaya, Jawa Timur 60293',
                'lat'       => -7.3201452,
                'lng'       => 112.7677418,
                'phone'     => '0812-3111-9001',
                'pic_name'  => 'Bambang Supriyanto (Kepala Hub)',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-JKT-01',
                'name'      => 'NitipDong Hub DC Jakarta Barat',
                'city'      => 'Jakarta Barat',
                'province'  => 'DKI Jakarta',
                'address'   => 'Kawasan Logistik Daan Mogot Km 12 No. 88, Cengkareng, Jakarta Barat, DKI Jakarta 11730',
                'lat'       => -6.1553120,
                'lng'       => 106.7584190,
                'phone'     => '0812-3111-9002',
                'pic_name'  => 'Hendra Wijaya',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-BDG-01',
                'name'      => 'NitipDong Hub DC Bandung',
                'city'      => 'Bandung',
                'province'  => 'Jawa Barat',
                'address'   => 'Jl. Soekarno-Hatta No. 590, Sekejati, Buahbatu, Kota Bandung, Jawa Barat 40286',
                'lat'       => -6.9482110,
                'lng'       => 107.6321450,
                'phone'     => '0812-3111-9003',
                'pic_name'  => 'Asep Sunandar',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-SMG-01',
                'name'      => 'NitipDong Hub DC Semarang',
                'city'      => 'Semarang',
                'province'  => 'Jawa Tengah',
                'address'   => 'Kawasan Industri Terboyo Blok M No. 15, Genuk, Semarang, Jawa Tengah 50118',
                'lat'       => -6.9612300,
                'lng'       => 110.4619200,
                'phone'     => '0812-3111-9004',
                'pic_name'  => 'Sugeng Riyadi',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-YOG-01',
                'name'      => 'NitipDong Hub DC Yogyakarta',
                'city'      => 'Yogyakarta',
                'province'  => 'DI Yogyakarta',
                'address'   => 'Jl. Ring Road Utara Km 8, Maguwoharjo, Depok, Sleman, DI Yogyakarta 55281',
                'lat'       => -7.7794200,
                'lng'       => 110.4312100,
                'phone'     => '0812-3111-9005',
                'pic_name'  => 'Agus Prasetyo',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-MLG-01',
                'name'      => 'NitipDong Hub DC Malang',
                'city'      => 'Malang',
                'province'  => 'Jawa Timur',
                'address'   => 'Jl. Raya Singosari No. 120, Singosari, Kabupaten Malang, Jawa Timur 65153',
                'lat'       => -7.8924100,
                'lng'       => 112.6648200,
                'phone'     => '0812-3111-9006',
                'pic_name'  => 'Dwi Cahyono',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-DPS-01',
                'name'      => 'NitipDong Hub DC Denpasar Bali',
                'city'      => 'Denpasar',
                'province'  => 'Bali',
                'address'   => 'Jl. Gatot Subroto Barat No. 340, Pemecutan Kaja, Denpasar Utara, Bali 80118',
                'lat'       => -8.6415200,
                'lng'       => 115.2163100,
                'phone'     => '0812-3111-9007',
                'pic_name'  => 'I Wayan Sudarma',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-MDN-01',
                'name'      => 'NitipDong Hub DC Medan',
                'city'      => 'Medan',
                'province'  => 'Sumatera Utara',
                'address'   => 'Kawasan Industri Medan (KIM 2), Jl. Pulau Nias No. 18, Mabar, Deli Serdang, Sumatera Utara 20242',
                'lat'       => 3.6698100,
                'lng'       => 98.6791200,
                'phone'     => '0812-3111-9008',
                'pic_name'  => 'Zulkifli Lubis',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-MKS-01',
                'name'      => 'NitipDong Hub DC Makassar',
                'city'      => 'Makassar',
                'province'  => 'Sulawesi Selatan',
                'address'   => 'Kawasan Industri Makassar (KIMA) Kavling 10, Tamalanrea, Makassar, Sulawesi Selatan 90245',
                'lat'       => -5.1124100,
                'lng'       => 119.5103200,
                'phone'     => '0812-3111-9009',
                'pic_name'  => 'Andi Mappatunru',
                'is_active' => true,
            ],
            [
                'code'      => 'DC-PLM-01',
                'name'      => 'NitipDong Hub DC Palembang',
                'city'      => 'Palembang',
                'province'  => 'Sumatera Selatan',
                'address'   => 'Jl. Bypass Alang-Alang Lebar Km 12 No. 55, Sukarami, Palembang, Sumatera Selatan 30154',
                'lat'       => -2.9234100,
                'lng'       => 104.7082100,
                'phone'     => '0812-3111-9010',
                'pic_name'  => 'Rahmat Hidayat',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $wh) {
            Warehouse::updateOrCreate(['code' => $wh['code']], $wh);
        }
    }
}
