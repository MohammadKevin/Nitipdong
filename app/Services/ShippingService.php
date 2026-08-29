<?php

namespace App\Services;

use App\Models\Store;

class ShippingService
{
    /**
     * Layanan Ekspedisi Internal NitipDongExpress (NDX).
     */
    public const COURIERS = [
        'ndx' => [
            'name'     => 'NitipDongExpress',
            'code'     => 'NDX',
            'icon'     => 'fa-solid fa-truck-fast',
            'services' => [
                'REG' => [
                    'name'           => 'NDX Reguler',
                    'etd'            => '2-3 Hari Kerja',
                    'base_cost'      => 9000,
                    'cost_per_kg'    => 4000,
                    'description'    => 'Pengiriman reguler via gudang NDX terdekat',
                    'same_city_only' => false,
                ],
                'EXPRESS' => [
                    'name'           => 'NDX Express',
                    'etd'            => '1 Hari Kerja',
                    'base_cost'      => 18000,
                    'cost_per_kg'    => 7000,
                    'description'    => 'Pengiriman kilat, dijamin sampai besok',
                    'same_city_only' => false,
                ],
                'SAME_DAY' => [
                    'name'           => 'NDX Same Day',
                    'etd'            => '6-8 Jam',
                    'base_cost'      => 25000,
                    'cost_per_kg'    => 9000,
                    'description'    => 'Khusus dalam kota — pengiriman di hari yang sama',
                    'same_city_only' => true,
                ],
            ],
        ],
    ];

    /**
     * Helper untuk memeriksa apakah dua kota berada di dalam 1 kota/wilayah yang sama (Same City).
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
     * Helper perkiraan apakah dua kota berada dalam 1 provinsi yang sama.
     */
    public static function isSameProvince(?string $cityA, ?string $cityB): bool
    {
        if (empty($cityA) || empty($cityB)) {
            return false;
        }

        if (self::isSameCity($cityA, $cityB)) {
            return true;
        }

        $jabodetabek = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'];
        $cleanA = strtolower($cityA);
        $cleanB = strtolower($cityB);

        $isAInJabo = false;
        $isBInJabo = false;
        foreach ($jabodetabek as $j) {
            if (str_contains($cleanA, $j)) $isAInJabo = true;
            if (str_contains($cleanB, $j)) $isBInJabo = true;
        }

        if ($isAInJabo && $isBInJabo) {
            return true;
        }

        // Check regional keywords
        $jatim = ['surabaya', 'malang', 'sidoarjo', 'gresik', 'kediri', 'jember', 'banyuwangi', 'madiun', 'blitar', 'pasuruan'];
        $isAJatim = false; $isBJatim = false;
        foreach ($jatim as $jt) {
            if (str_contains($cleanA, $jt)) $isAJatim = true;
            if (str_contains($cleanB, $jt)) $isBJatim = true;
        }
        if ($isAJatim && $isBJatim) return true;

        $jateng = ['semarang', 'solo', 'surakarta', 'magelang', 'yogyakarta', 'jogja', 'pekalongan', 'tegal', 'purwokerto'];
        $isAJateng = false; $isBJateng = false;
        foreach ($jateng as $jg) {
            if (str_contains($cleanA, $jg)) $isAJateng = true;
            if (str_contains($cleanB, $jg)) $isBJateng = true;
        }
        if ($isAJateng && $isBJateng) return true;

        $jabar = ['bandung', 'cimahi', 'cirebon', 'tasikmalaya', 'sukabumi', 'garut', 'karawang', 'purwakarta'];
        $isAJabar = false; $isBJabar = false;
        foreach ($jabar as $jb) {
            if (str_contains($cleanA, $jb)) $isAJabar = true;
            if (str_contains($cleanB, $jb)) $isBJabar = true;
        }
        if ($isAJabar && $isBJabar) return true;

        return false;
    }

    /**
     * Hitung ongkos kirim NitipDongExpress (NDX) berdasarkan berat, kota toko, dan alamat pembeli.
     */
    public static function calculateRate(
        string $courierCode = 'ndx',
        string $serviceCode = 'REG',
        float $weightInKg = 1.0,
        ?string $destinationCity = null,
        ?string $originCity = null,
        ?Store $store = null
    ): array {
        $courier = self::COURIERS['ndx'];
        $serviceKey = strtoupper($serviceCode);
        $service = $courier['services'][$serviceKey] ?? $courier['services']['REG'];

        $origin = $originCity ?: ($store?->effective_city ?: 'Jakarta Pusat');
        $destination = $destinationCity ?: 'Jakarta Pusat';

        $chargeableWeight = max(1.0, ceil($weightInKg));
        $isSameCity = self::isSameCity($origin, $destination);
        $isSameProvince = self::isSameProvince($origin, $destination);

        $normalBaseCost = $service['base_cost'];
        $normalAdditionalWeightCost = ($chargeableWeight - 1) * $service['cost_per_kg'];
        $normalTotalCost = $normalBaseCost + $normalAdditionalWeightCost;

        if ($isSameCity) {
            if ($serviceKey === 'REG') {
                $finalCost = 0;
                $isFree = true;
                $etd = '1 Hari (Dalam Kota)';
                $badge = 'Gratis Ongkir (1 Kota)';
            } elseif ($serviceKey === 'EXPRESS') {
                $finalCost = (int) round($normalTotalCost * 0.5);
                $isFree = false;
                $etd = 'Besok Sampai';
                $badge = 'Diskon 50% (Dalam Kota)';
            } else {
                // SAME_DAY
                $finalCost = (int) $normalTotalCost;
                $isFree = false;
                $etd = '6-8 Jam (Hari Ini)';
                $badge = 'Same Day Delivery';
            }
        } elseif ($isSameProvince) {
            if ($serviceKey === 'EXPRESS') {
                $finalCost = (int) round($normalTotalCost * 1.2);
                $etd = '1 Hari Kerja';
                $badge = 'Kilat';
            } else {
                $finalCost = (int) $normalTotalCost;
                $etd = '2-3 Hari Kerja';
                $badge = null;
            }
            $isFree = false;
        } else {
            // Antar Provinsi
            if ($serviceKey === 'EXPRESS') {
                $finalCost = (int) round($normalTotalCost * 1.5);
                $etd = '1-2 Hari Kerja';
                $badge = 'Antar Provinsi';
            } else {
                $finalCost = (int) ($normalTotalCost + 5000);
                $etd = '3-4 Hari Kerja';
                $badge = null;
            }
            $isFree = false;
        }

        return [
            'courier_code'            => 'NDX',
            'courier_name'            => 'NitipDongExpress',
            'service_code'            => $serviceKey,
            'service_name'            => $service['name'],
            'description'             => $service['description'],
            'etd'                     => $etd,
            'weight'                  => $chargeableWeight,
            'origin_city'             => $origin,
            'destination_city'        => $destination,
            'is_same_city'            => $isSameCity,
            'is_same_province'        => $isSameProvince,
            'is_free_shipping'        => $isFree,
            'badge'                   => $badge,
            'original_cost'           => (int) $normalTotalCost,
            'formatted_original_cost' => 'Rp ' . number_format($normalTotalCost, 0, ',', '.'),
            'cost'                    => $finalCost,
            'formatted_cost'          => $isFree ? 'Gratis Ongkir (Rp 0)' : ('Rp ' . number_format($finalCost, 0, ',', '.')),
        ];
    }

    /**
     * Dapatkan semua opsi layanan NDX yang tersedia untuk keranjang/checkout.
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
        $isSameCity = self::isSameCity($origin, $destinationCity);

        foreach (self::COURIERS['ndx']['services'] as $sKey => $service) {
            // Same Day hanya tersedia jika 1 kota
            if ($service['same_city_only'] && !$isSameCity) {
                continue;
            }

            $rate = self::calculateRate('ndx', $sKey, $weight, $destinationCity, $origin, $store);
            $options[] = [
                'id'                      => "NDX_{$sKey}",
                'courier_code'            => 'NDX',
                'courier_name'            => 'NitipDongExpress',
                'courier_icon'            => 'fa-solid fa-truck-fast',
                'service_code'            => $sKey,
                'service_name'            => $service['name'],
                'description'             => $service['description'],
                'etd'                     => $rate['etd'],
                'cost'                    => $rate['cost'],
                'original_cost'           => $rate['original_cost'],
                'is_same_city'            => $rate['is_same_city'],
                'is_free_shipping'        => $rate['is_free_shipping'],
                'badge'                   => $rate['badge'],
                'formatted_cost'          => $rate['formatted_cost'],
                'formatted_original_cost' => $rate['formatted_original_cost'],
            ];
        }

        return $options;
    }

    /**
     * Default courier option (NDX Reguler).
     */
    public static function getDefaultOption(
        float $totalWeight = 1.0,
        ?string $destinationCity = null,
        ?string $originCity = null,
        ?Store $store = null
    ): array {
        return self::calculateRate('ndx', 'REG', $totalWeight, $destinationCity, $originCity, $store);
    }
}

