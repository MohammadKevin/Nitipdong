<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndonesianRegionService
{
    /**
     * Base URL for open Indonesian Administrative Region API (Kemendagri / BPS).
     */
    private const BASE_API_URL = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Daftar 38 Provinsi Resmi di Indonesia beserta kode Kemendagri & koordinat geografis.
     */
    public const OFFICIAL_PROVINCES = [
        ['id' => '11', 'name' => 'Aceh', 'lat' => 4.6951, 'lng' => 96.7494],
        ['id' => '12', 'name' => 'Sumatera Utara', 'lat' => 2.1154, 'lng' => 99.5451],
        ['id' => '13', 'name' => 'Sumatera Barat', 'lat' => -0.7399, 'lng' => 100.8000],
        ['id' => '14', 'name' => 'Riau', 'lat' => 0.2933, 'lng' => 101.7068],
        ['id' => '15', 'name' => 'Jambi', 'lat' => -1.6101, 'lng' => 103.6131],
        ['id' => '16', 'name' => 'Sumatera Selatan', 'lat' => -3.3194, 'lng' => 104.9144],
        ['id' => '17', 'name' => 'Bengkulu', 'lat' => -3.5778, 'lng' => 102.3464],
        ['id' => '18', 'name' => 'Lampung', 'lat' => -4.5586, 'lng' => 105.4068],
        ['id' => '19', 'name' => 'Kepulauan Bangka Belitung', 'lat' => -2.7411, 'lng' => 106.4406],
        ['id' => '21', 'name' => 'Kepulauan Riau', 'lat' => 3.9456, 'lng' => 108.1429],
        ['id' => '31', 'name' => 'DKI Jakarta', 'lat' => -6.2088, 'lng' => 106.8456],
        ['id' => '32', 'name' => 'Jawa Barat', 'lat' => -6.9175, 'lng' => 107.6191],
        ['id' => '33', 'name' => 'Jawa Tengah', 'lat' => -7.1509, 'lng' => 110.1402],
        ['id' => '34', 'name' => 'DI Yogyakarta', 'lat' => -7.7956, 'lng' => 110.3695],
        ['id' => '35', 'name' => 'Jawa Timur', 'lat' => -7.5361, 'lng' => 112.2384],
        ['id' => '36', 'name' => 'Banten', 'lat' => -6.4058, 'lng' => 106.0640],
        ['id' => '51', 'name' => 'Bali', 'lat' => -8.4095, 'lng' => 115.1889],
        ['id' => '52', 'name' => 'Nusa Tenggara Barat', 'lat' => -8.6529, 'lng' => 117.3616],
        ['id' => '53', 'name' => 'Nusa Tenggara Timur', 'lat' => -8.6574, 'lng' => 121.0794],
        ['id' => '61', 'name' => 'Kalimantan Barat', 'lat' => -0.2787, 'lng' => 111.4753],
        ['id' => '62', 'name' => 'Kalimantan Tengah', 'lat' => -1.6815, 'lng' => 113.3824],
        ['id' => '63', 'name' => 'Kalimantan Selatan', 'lat' => -3.0926, 'lng' => 115.2838],
        ['id' => '64', 'name' => 'Kalimantan Timur', 'lat' => 0.5387, 'lng' => 116.4194],
        ['id' => '65', 'name' => 'Kalimantan Utara', 'lat' => 3.0731, 'lng' => 116.0414],
        ['id' => '71', 'name' => 'Sulawesi Utara', 'lat' => 0.6247, 'lng' => 123.9750],
        ['id' => '72', 'name' => 'Sulawesi Tengah', 'lat' => -1.4300, 'lng' => 121.4456],
        ['id' => '73', 'name' => 'Sulawesi Selatan', 'lat' => -3.6687, 'lng' => 119.9741],
        ['id' => '74', 'name' => 'Sulawesi Tenggara', 'lat' => -4.1449, 'lng' => 122.1746],
        ['id' => '75', 'name' => 'Gorontalo', 'lat' => 0.6999, 'lng' => 122.4467],
        ['id' => '76', 'name' => 'Sulawesi Barat', 'lat' => -2.8441, 'lng' => 119.2321],
        ['id' => '81', 'name' => 'Maluku', 'lat' => -3.2385, 'lng' => 130.1453],
        ['id' => '82', 'name' => 'Maluku Utara', 'lat' => 1.5709, 'lng' => 127.8088],
        ['id' => '91', 'name' => 'Papua', 'lat' => -4.2699, 'lng' => 138.0804],
        ['id' => '92', 'name' => 'Papua Barat', 'lat' => -1.3361, 'lng' => 133.1747],
        ['id' => '93', 'name' => 'Papua Selatan', 'lat' => -7.5000, 'lng' => 139.5000],
        ['id' => '94', 'name' => 'Papua Tengah', 'lat' => -3.8000, 'lng' => 136.5000],
        ['id' => '95', 'name' => 'Papua Pegunungan', 'lat' => -4.0000, 'lng' => 139.0000],
        ['id' => '96', 'name' => 'Papua Barat Daya', 'lat' => -1.0000, 'lng' => 131.5000],
    ];

    /**
     * Backward compatibility constant for existing UI components.
     */
    public const PROVINCES_DATA = [
        'DKI Jakarta' => [
            'lat' => -6.2088, 'lng' => 106.8456,
            'cities' => [
                'Jakarta Pusat' => ['lat' => -6.1805, 'lng' => 106.8284, 'postal' => '10110'],
                'Jakarta Selatan' => ['lat' => -6.2615, 'lng' => 106.8106, 'postal' => '12110'],
                'Jakarta Barat' => ['lat' => -6.1674, 'lng' => 106.7637, 'postal' => '11410'],
                'Jakarta Timur' => ['lat' => -6.2250, 'lng' => 106.9004, 'postal' => '13310'],
                'Jakarta Utara' => ['lat' => -6.1214, 'lng' => 106.8824, 'postal' => '14110'],
                'Kepulauan Seribu' => ['lat' => -5.6122, 'lng' => 106.5622, 'postal' => '14510'],
            ]
        ],
        'Jawa Barat' => [
            'lat' => -6.9175, 'lng' => 107.6191,
            'cities' => [
                'Kota Bandung' => ['lat' => -6.9175, 'lng' => 107.6191, 'postal' => '40111'],
                'Kabupaten Bandung' => ['lat' => -7.0252, 'lng' => 107.5198, 'postal' => '40311'],
                'Kota Bekasi' => ['lat' => -6.2383, 'lng' => 106.9756, 'postal' => '17111'],
                'Kabupaten Bekasi' => ['lat' => -6.3645, 'lng' => 107.1725, 'postal' => '17530'],
                'Kota Bogor' => ['lat' => -6.5971, 'lng' => 106.8060, 'postal' => '16111'],
                'Kabupaten Bogor' => ['lat' => -6.4741, 'lng' => 106.8299, 'postal' => '16911'],
                'Kota Depok' => ['lat' => -6.4025, 'lng' => 106.7942, 'postal' => '16411'],
                'Kota Cimahi' => ['lat' => -6.8722, 'lng' => 107.5422, 'postal' => '40511'],
                'Kota Cirebon' => ['lat' => -6.7320, 'lng' => 108.5523, 'postal' => '45111'],
                'Kabupaten Cirebon' => ['lat' => -6.7656, 'lng' => 108.4812, 'postal' => '45611'],
                'Kota Sukabumi' => ['lat' => -6.9277, 'lng' => 106.9299, 'postal' => '43111'],
                'Kota Tasikmalaya' => ['lat' => -7.3274, 'lng' => 108.2207, 'postal' => '46111'],
                'Kabupaten Karawang' => ['lat' => -6.3042, 'lng' => 107.3075, 'postal' => '41311'],
                'Kabupaten Purwakarta' => ['lat' => -6.5569, 'lng' => 107.4433, 'postal' => '41111'],
            ]
        ],
        'Jawa Tengah' => [
            'lat' => -7.1509, 'lng' => 110.1402,
            'cities' => [
                'Kota Semarang' => ['lat' => -6.9667, 'lng' => 110.4167, 'postal' => '50111'],
                'Kota Surakarta (Solo)' => ['lat' => -7.5755, 'lng' => 110.8243, 'postal' => '57111'],
                'Kota Magelang' => ['lat' => -7.4706, 'lng' => 110.2178, 'postal' => '56111'],
                'Kota Salatiga' => ['lat' => -7.3305, 'lng' => 110.5084, 'postal' => '50711'],
                'Kota Tegal' => ['lat' => -6.8694, 'lng' => 109.1402, 'postal' => '52111'],
                'Kota Pekalongan' => ['lat' => -6.8886, 'lng' => 109.6753, 'postal' => '51111'],
                'Kabupaten Banyumas (Purwokerto)' => ['lat' => -7.4243, 'lng' => 109.2302, 'postal' => '53111'],
                'Kabupaten Kudus' => ['lat' => -6.8048, 'lng' => 110.8405, 'postal' => '59311'],
            ]
        ],
        'DI Yogyakarta' => [
            'lat' => -7.7956, 'lng' => 110.3695,
            'cities' => [
                'Kota Yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695, 'postal' => '55111'],
                'Kabupaten Sleman' => ['lat' => -7.6894, 'lng' => 110.3444, 'postal' => '55511'],
                'Kabupaten Bantul' => ['lat' => -7.8878, 'lng' => 110.3283, 'postal' => '55711'],
                'Kabupaten Gunungkidul' => ['lat' => -7.9606, 'lng' => 110.6033, 'postal' => '55811'],
                'Kabupaten Kulon Progo' => ['lat' => -7.8286, 'lng' => 110.1583, 'postal' => '55611'],
            ]
        ],
        'Jawa Timur' => [
            'lat' => -7.5361, 'lng' => 112.2384,
            'cities' => [
                'Kota Surabaya' => ['lat' => -7.2575, 'lng' => 112.7521, 'postal' => '60111'],
                'Kota Malang' => ['lat' => -7.9797, 'lng' => 112.6304, 'postal' => '65111'],
                'Kabupaten Sidoarjo' => ['lat' => -7.4726, 'lng' => 112.6675, 'postal' => '61211'],
                'Kabupaten Gresik' => ['lat' => -7.1594, 'lng' => 112.6517, 'postal' => '61111'],
                'Kota Kediri' => ['lat' => -7.8480, 'lng' => 112.0178, 'postal' => '64111'],
                'Kota Batu' => ['lat' => -7.8711, 'lng' => 112.5272, 'postal' => '65311'],
                'Kabupaten Jember' => ['lat' => -8.1845, 'lng' => 113.6681, 'postal' => '68111'],
                'Kabupaten Banyuwangi' => ['lat' => -8.2192, 'lng' => 114.3691, 'postal' => '68411'],
            ]
        ],
        'Banten' => [
            'lat' => -6.4058, 'lng' => 106.0640,
            'cities' => [
                'Kota Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319, 'postal' => '15111'],
                'Kota Tangerang Selatan' => ['lat' => -6.2889, 'lng' => 106.7179, 'postal' => '15411'],
                'Kota Serang' => ['lat' => -6.1200, 'lng' => 106.1503, 'postal' => '42111'],
                'Kota Cilegon' => ['lat' => -5.9961, 'lng' => 106.0158, 'postal' => '42411'],
            ]
        ],
        'Bali' => [
            'lat' => -8.4095, 'lng' => 115.1889,
            'cities' => [
                'Kota Denpasar' => ['lat' => -8.6705, 'lng' => 115.2126, 'postal' => '80111'],
                'Kabupaten Badung (Kuta/Canggu)' => ['lat' => -8.5819, 'lng' => 115.1771, 'postal' => '80361'],
                'Kabupaten Gianyar (Ubud)' => ['lat' => -8.5397, 'lng' => 115.3262, 'postal' => '80511'],
                'Kabupaten Buleleng' => ['lat' => -8.1120, 'lng' => 115.0882, 'postal' => '81111'],
                'Kabupaten Tabanan' => ['lat' => -8.5411, 'lng' => 115.1250, 'postal' => '82111'],
            ]
        ],
        'Sumatera Utara' => [
            'lat' => 2.1154, 'lng' => 99.5451,
            'cities' => [
                'Kota Medan' => ['lat' => 3.5952, 'lng' => 98.6722, 'postal' => '20111'],
                'Kota Binjai' => ['lat' => 3.6006, 'lng' => 98.4854, 'postal' => '20711'],
                'Kota Pematangsiantar' => ['lat' => 2.9610, 'lng' => 99.0682, 'postal' => '21111'],
                'Kabupaten Deli Serdang' => ['lat' => 3.4167, 'lng' => 98.7000, 'postal' => '20511'],
            ]
        ],
        'Sulawesi Selatan' => [
            'lat' => -3.6687, 'lng' => 119.9741,
            'cities' => [
                'Kota Makassar' => ['lat' => -5.1477, 'lng' => 119.4327, 'postal' => '90111'],
                'Kabupaten Gowa' => ['lat' => -5.2000, 'lng' => 119.5000, 'postal' => '92111'],
                'Kota Parepare' => ['lat' => -4.0139, 'lng' => 119.6256, 'postal' => '91111'],
            ]
        ],
    ];

    /**
     * Get list of all 38 Indonesian Provinces.
     */
    public static function getProvinces(): array
    {
        return Cache::remember('indo_provinces_list', 86400 * 30, function () {
            try {
                $response = Http::timeout(4)->get(self::BASE_API_URL . '/provinces.json');
                if ($response->successful() && is_array($response->json())) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::warning('Failed fetching provinces from open API, using local fallback: ' . $e->getMessage());
            }

            return array_map(fn($p) => ['id' => $p['id'], 'name' => $p['name']], self::OFFICIAL_PROVINCES);
        });
    }

    /**
     * Get Cities / Regencies (Kabupaten / Kota) for a given Province ID.
     */
    public static function getRegencies(string $provinceId): array
    {
        return Cache::remember("indo_regencies_{$provinceId}", 86400 * 30, function () use ($provinceId) {
            try {
                $response = Http::timeout(4)->get(self::BASE_API_URL . "/regencies/{$provinceId}.json");
                if ($response->successful() && is_array($response->json())) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::warning("Failed fetching regencies for province {$provinceId}: " . $e->getMessage());
            }

            // Local fallback mapping
            $provName = self::getProvinceNameById($provinceId);
            if ($provName && isset(self::PROVINCES_DATA[$provName])) {
                $results = [];
                $idx = 1;
                foreach (self::PROVINCES_DATA[$provName]['cities'] as $name => $data) {
                    $results[] = [
                        'id' => $provinceId . str_pad((string)$idx++, 2, '0', STR_PAD_LEFT),
                        'province_id' => $provinceId,
                        'name' => $name,
                    ];
                }
                return $results;
            }

            return [];
        });
    }

    /**
     * Get Districts (Kecamatan) for a given Regency / City ID.
     */
    public static function getDistricts(string $regencyId): array
    {
        return Cache::remember("indo_districts_{$regencyId}", 86400 * 30, function () use ($regencyId) {
            try {
                $response = Http::timeout(4)->get(self::BASE_API_URL . "/districts/{$regencyId}.json");
                if ($response->successful() && is_array($response->json())) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::warning("Failed fetching districts for regency {$regencyId}: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Get Villages (Kelurahan / Desa) for a given District ID.
     */
    public static function getVillages(string $districtId): array
    {
        return Cache::remember("indo_villages_{$districtId}", 86400 * 30, function () use ($districtId) {
            try {
                $response = Http::timeout(4)->get(self::BASE_API_URL . "/villages/{$districtId}.json");
                if ($response->successful() && is_array($response->json())) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::warning("Failed fetching villages for district {$districtId}: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Reverse Geocode coordinates to street address and regions via OpenStreetMap Nominatim.
     */
    public static function reverseGeocode(float $latitude, float $longitude): array
    {
        $cacheKey = "geo_rev_" . round($latitude, 4) . "_" . round($longitude, 4);

        return Cache::remember($cacheKey, 86400 * 7, function () use ($latitude, $longitude) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'NitipDong-ECommerce-App/1.0',
                ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'         => 'json',
                    'lat'            => $latitude,
                    'lon'            => $longitude,
                    'addressdetails' => 1,
                    'zoom'           => 18,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $addr = $data['address'] ?? [];

                    return [
                        'success'      => true,
                        'display_name' => $data['display_name'] ?? '',
                        'street'       => $addr['road'] ?? $addr['pedestrian'] ?? $addr['suburb'] ?? '',
                        'village'      => $addr['village'] ?? $addr['hamlet'] ?? $addr['suburb'] ?? '',
                        'district'     => $addr['city_district'] ?? $addr['suburb'] ?? $addr['town'] ?? '',
                        'city'         => $addr['city'] ?? $addr['county'] ?? $addr['regency'] ?? '',
                        'province'     => $addr['state'] ?? '',
                        'postal_code'  => $addr['postcode'] ?? '',
                        'latitude'     => (string) $latitude,
                        'longitude'    => (string) $longitude,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Reverse geocoding failed for ({$latitude}, {$longitude}): " . $e->getMessage());
            }

            return [
                'success'      => false,
                'display_name' => '',
                'latitude'     => (string) $latitude,
                'longitude'    => (string) $longitude,
            ];
        });
    }

    private static function getProvinceNameById(string $id): ?string
    {
        foreach (self::OFFICIAL_PROVINCES as $p) {
            if ($p['id'] === $id) {
                return $p['name'];
            }
        }
        return null;
    }
}
