<?php

namespace App\Services;

use App\Models\Store;
use App\Models\UserAddress;

class ShippingService
{
    /**
     * Daftar kurir dan paket layanan yang didukung.
     */
    public const COURIERS = [
        'jne' => [
            'name' => 'JNE Express',
            'code' => 'JNE',
            'icon' => 'fa-solid fa-truck',
            'services' => [
                'REG' => ['name' => 'JNE Reguler', 'etd' => '2-3 Hari', 'base_cost' => 12000, 'cost_per_kg' => 8000],
                'YES' => ['name' => 'JNE YES (Yakin Esok Sampai)', 'etd' => '1 Hari', 'base_cost' => 24000, 'cost_per_kg' => 15000],
                'JTR' => ['name' => 'JNE Trucking (Kargo > 10kg)', 'etd' => '3-5 Hari', 'base_cost' => 35000, 'cost_per_kg' => 3000],
            ],
        ],
        'jnt' => [
            'name' => 'J&T Express',
            'code' => 'J&T',
            'icon' => 'fa-solid fa-truck-fast',
            'services' => [
                'EZ'    => ['name' => 'J&T EZ (Reguler)', 'etd' => '2-3 Hari', 'base_cost' => 11000, 'cost_per_kg' => 7500],
                'SUPER' => ['name' => 'J&T Super (Kilat)', 'etd' => '1 Hari', 'base_cost' => 22000, 'cost_per_kg' => 14000],
            ],
        ],
        'sicepat' => [
            'name' => 'SiCepat Ekspres',
            'code' => 'SICEPAT',
            'icon' => 'fa-solid fa-bolt-lightning',
            'services' => [
                'REG'  => ['name' => 'SiCepat Reguler', 'etd' => '2-3 Hari', 'base_cost' => 11500, 'cost_per_kg' => 7500],
                'BEST' => ['name' => 'SiCepat BEST (Besok Sampai)', 'etd' => '1 Hari', 'base_cost' => 23000, 'cost_per_kg' => 14500],
                'GOKIL'=> ['name' => 'SiCepat GOKIL (Kargo)', 'etd' => '3-5 Hari', 'base_cost' => 30000, 'cost_per_kg' => 3500],
            ],
        ],
        'pos' => [
            'name' => 'POS Indonesia',
            'code' => 'POS',
            'icon' => 'fa-solid fa-box-archive',
            'services' => [
                'KILAT'   => ['name' => 'Pos Kilat Khusus', 'etd' => '2-4 Hari', 'base_cost' => 10000, 'cost_per_kg' => 7000],
                'NEXTDAY' => ['name' => 'Pos Next Day', 'etd' => '1 Hari', 'base_cost' => 20000, 'cost_per_kg' => 13000],
            ],
        ],
        'instant' => [
            'name' => 'Instant & Same Day',
            'code' => 'INSTANT',
            'icon' => 'fa-solid fa-motorcycle',
            'services' => [
                'SAMEDAY' => ['name' => 'GoSend / Grab Same Day', 'etd' => '6-8 Jam', 'base_cost' => 18000, 'cost_per_kg' => 5000],
                'INSTANT' => ['name' => 'GoSend / Grab Instant (Kilat)', 'etd' => '1-2 Jam', 'base_cost' => 28000, 'cost_per_kg' => 8000],
            ],
        ],
    ];

    /**
     * Hitung ongkos kirim berdasarkan berat (kg) dan kota tujuan.
     */
    public static function calculateRate(string $courierCode, string $serviceCode, float $weightInKg = 1.0, ?string $destinationCity = null): array
    {
        $courierKey = strtolower($courierCode);
        $serviceKey = strtoupper($serviceCode);

        $courier = self::COURIERS[$courierKey] ?? self::COURIERS['jne'];
        $service = $courier['services'][$serviceKey] ?? reset($courier['services']);
        $serviceNameKey = key($courier['services']);

        // Berat minimal dihitung 1 kg
        $chargeableWeight = max(1.0, ceil($weightInKg));

        $baseCost = $service['base_cost'];
        $additionalWeightCost = ($chargeableWeight - 1) * $service['cost_per_kg'];
        $totalCost = $baseCost + $additionalWeightCost;

        return [
            'courier_code'    => $courier['code'],
            'courier_name'    => $courier['name'],
            'service_code'    => $serviceKey,
            'service_name'    => $service['name'],
            'etd'             => $service['etd'],
            'weight'          => $chargeableWeight,
            'cost'            => (int) $totalCost,
            'formatted_cost'  => 'Rp ' . number_format($totalCost, 0, ',', '.'),
        ];
    }

    /**
     * Dapatkan semua opsi pengiriman yang tersedia untuk sebuah toko/keranjang.
     */
    public static function getAvailableOptions(float $totalWeight = 1.0, ?string $destinationCity = null): array
    {
        $options = [];
        $weight = max(1.0, $totalWeight);

        foreach (self::COURIERS as $cKey => $courier) {
            foreach ($courier['services'] as $sKey => $service) {
                $rate = self::calculateRate($cKey, $sKey, $weight, $destinationCity);
                $options[] = [
                    'id'              => "{$courier['code']}_{$sKey}",
                    'courier_code'    => $courier['code'],
                    'courier_name'    => $courier['name'],
                    'courier_icon'    => $courier['icon'],
                    'service_code'    => $sKey,
                    'service_name'    => $service['name'],
                    'etd'             => $service['etd'],
                    'cost'            => $rate['cost'],
                    'formatted_cost'  => $rate['formatted_cost'],
                ];
            }
        }

        return $options;
    }

    /**
     * Default default courier option (JNE Reguler).
     */
    public static function getDefaultOption(float $totalWeight = 1.0): array
    {
        return self::calculateRate('jne', 'REG', $totalWeight);
    }
}
