<?php

namespace App\Services;

use App\Models\Store;

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
     * Helper untuk memeriksa apakah dua kota berada di dalam 1 kota yang sama (Same City).
     */
    public static function isSameCity(?string $cityA, ?string $cityB): bool
    {
        if (empty($cityA) || empty($cityB)) {
            return false;
        }

        $clean = function (string $c): string {
            $c = strtolower($c);
            $c = str_replace([
                'kota ', 'kabupaten ', 'kab. ', 'adm. ', 'administratif ',
                'dki ', 'daerah khusus ibukota ', 'wilayah '
            ], '', $c);
            // Standarisasi Jakarta (Semua wilayah Jakarta dihitung 1 kota aglomerasi gratis ongkir)
            if (str_contains($c, 'jakarta')) {
                return 'jakarta';
            }
            return trim($c);
        };

        $normA = $clean($cityA);
        $normB = $clean($cityB);

        if ($normA === $normB) {
            return true;
        }

        // Substring check (e.g. "Bandung" matches "Kota Bandung" or "Kabupaten Bandung")
        if (strlen($normA) >= 4 && strlen($normB) >= 4) {
            if (str_contains($normA, $normB) || str_contains($normB, $normA)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hitung ongkos kirim berdasarkan berat (kg), kota asal toko, dan kota tujuan pelanggan.
     * Jika DALAM 1 KOTA (Same City), maka GRATIS ONGKIR (Rp 0)!
     */
    public static function calculateRate(
        string $courierCode,
        string $serviceCode,
        float $weightInKg = 1.0,
        ?string $destinationCity = null,
        ?string $originCity = null,
        ?Store $store = null
    ): array {
        $courierKey = strtolower($courierCode);
        $serviceKey = strtoupper($serviceCode);

        $courier = self::COURIERS[$courierKey] ?? self::COURIERS['jne'];
        $service = $courier['services'][$serviceKey] ?? reset($courier['services']);

        // Tentukan kota asal dari Toko atau default
        $origin = $originCity ?: ($store?->effective_city ?: 'Jakarta Pusat');
        $destination = $destinationCity ?: 'Jakarta Pusat';

        // Berat minimal dihitung 1 kg
        $chargeableWeight = max(1.0, ceil($weightInKg));

        // Cek apakah Toko dan Pembeli berada DALAM 1 KOTA
        $isSameCity = self::isSameCity($origin, $destination);

        $normalBaseCost = $service['base_cost'];
        $normalAdditionalWeightCost = ($chargeableWeight - 1) * $service['cost_per_kg'];
        $normalTotalCost = $normalBaseCost + $normalAdditionalWeightCost;

        if ($isSameCity) {
            // GRATIS ONGKIR Rp 0 dalam 1 kota untuk semua kurir reguler dan same-day/instant
            $finalCost = 0;
            $isFree = true;
            $etd = in_array($courierKey, ['instant']) ? '1-3 Jam' : '1 Hari (Dalam Kota)';
            $badge = 'Gratis Ongkir (1 Kota)';
        } else {
            $finalCost = (int) $normalTotalCost;
            $isFree = false;
            $etd = $service['etd'];
            $badge = null;
        }

        return [
            'courier_code'    => $courier['code'],
            'courier_name'    => $courier['name'],
            'service_code'    => $serviceKey,
            'service_name'    => $service['name'],
            'etd'             => $etd,
            'weight'          => $chargeableWeight,
            'origin_city'     => $origin,
            'destination_city'=> $destination,
            'is_same_city'    => $isSameCity,
            'is_free_shipping'=> $isFree,
            'badge'           => $badge,
            'original_cost'   => (int) $normalTotalCost,
            'formatted_original_cost' => 'Rp ' . number_format($normalTotalCost, 0, ',', '.'),
            'cost'            => $finalCost,
            'formatted_cost'  => $isFree ? 'Rp 0' : ('Rp ' . number_format($finalCost, 0, ',', '.')),
        ];
    }

    /**
     * Dapatkan semua opsi pengiriman yang tersedia untuk sebuah toko/keranjang.
     */
    public static function getAvailableOptions(
        float $totalWeight = 1.0,
        ?string $destinationCity = null,
        ?string $originCity = null,
        ?Store $store = null
    ): array {
        $options = [];
        $weight = max(1.0, $totalWeight);
        $origin = $originCity ?: ($store?->effective_city ?: 'Jakarta Pusat');

        foreach (self::COURIERS as $cKey => $courier) {
            foreach ($courier['services'] as $sKey => $service) {
                $rate = self::calculateRate($cKey, $sKey, $weight, $destinationCity, $origin, $store);
                $options[] = [
                    'id'              => "{$courier['code']}_{$sKey}",
                    'courier_code'    => $courier['code'],
                    'courier_name'    => $courier['name'],
                    'courier_icon'    => $courier['icon'],
                    'service_code'    => $sKey,
                    'service_name'    => $service['name'],
                    'etd'             => $rate['etd'],
                    'cost'            => $rate['cost'],
                    'original_cost'   => $rate['original_cost'],
                    'is_same_city'    => $rate['is_same_city'],
                    'is_free_shipping'=> $rate['is_free_shipping'],
                    'badge'           => $rate['badge'],
                    'formatted_cost'  => $rate['formatted_cost'],
                    'formatted_original_cost' => $rate['formatted_original_cost'],
                ];
            }
        }

        return $options;
    }

    /**
     * Default courier option (JNE Reguler / Gratis Ongkir).
     */
    public static function getDefaultOption(
        float $totalWeight = 1.0,
        ?string $destinationCity = null,
        ?string $originCity = null,
        ?Store $store = null
    ): array {
        return self::calculateRate('jne', 'REG', $totalWeight, $destinationCity, $originCity, $store);
    }
}
