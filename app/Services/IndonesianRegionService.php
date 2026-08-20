<?php

namespace App\Services;

class IndonesianRegionService
{
    /**
     * Data 38 Provinsi di Indonesia beserta kota/kabupaten utama dan koordinat geografis.
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
                'Kabupaten Kulon Progo' => ['lat' => -7.7714, 'lng' => 110.1778, 'postal' => '55611'],
            ]
        ],
        'Jawa Timur' => [
            'lat' => -7.5360, 'lng' => 112.2384,
            'cities' => [
                'Kota Surabaya' => ['lat' => -7.2575, 'lng' => 112.7521, 'postal' => '60111'],
                'Kota Malang' => ['lat' => -7.9666, 'lng' => 112.6326, 'postal' => '65111'],
                'Kota Sidoarjo' => ['lat' => -7.4478, 'lng' => 112.7183, 'postal' => '61211'],
                'Kota Kediri' => ['lat' => -7.8480, 'lng' => 112.0178, 'postal' => '64111'],
                'Kota Madiun' => ['lat' => -7.6298, 'lng' => 111.5239, 'postal' => '63111'],
                'Kota Probolinggo' => ['lat' => -7.7543, 'lng' => 113.2159, 'postal' => '67211'],
                'Kota Pasuruan' => ['lat' => -7.6453, 'lng' => 112.9075, 'postal' => '67111'],
                'Kota Batu' => ['lat' => -7.8712, 'lng' => 112.5271, 'postal' => '65311'],
                'Kabupaten Gresik' => ['lat' => -7.1566, 'lng' => 112.6555, 'postal' => '61111'],
                'Kabupaten Jember' => ['lat' => -8.1724, 'lng' => 113.7007, 'postal' => '68111'],
                'Kabupaten Banyuwangi' => ['lat' => -8.2191, 'lng' => 114.3691, 'postal' => '68411'],
            ]
        ],
        'Banten' => [
            'lat' => -6.4058, 'lng' => 106.0640,
            'cities' => [
                'Kota Tangerang' => ['lat' => -6.1783, 'lng' => 106.6319, 'postal' => '15111'],
                'Kota Tangerang Selatan' => ['lat' => -6.2887, 'lng' => 106.7179, 'postal' => '15411'],
                'Kabupaten Tangerang' => ['lat' => -6.1969, 'lng' => 106.4777, 'postal' => '15710'],
                'Kota Serang' => ['lat' => -6.1104, 'lng' => 106.1639, 'postal' => '42111'],
                'Kota Cilegon' => ['lat' => -6.0174, 'lng' => 106.0538, 'postal' => '42411'],
            ]
        ],
        'Bali' => [
            'lat' => -8.4095, 'lng' => 115.1889,
            'cities' => [
                'Kota Denpasar' => ['lat' => -8.6705, 'lng' => 115.2126, 'postal' => '80111'],
                'Kabupaten Badung (Kuta/Canggu)' => ['lat' => -8.5819, 'lng' => 115.1771, 'postal' => '80351'],
                'Kabupaten Gianyar (Ubud)' => ['lat' => -8.5375, 'lng' => 115.3262, 'postal' => '80511'],
                'Kabupaten Tabanan' => ['lat' => -8.5397, 'lng' => 115.1256, 'postal' => '82111'],
                'Kabupaten Buleleng (Singaraja)' => ['lat' => -8.1120, 'lng' => 115.0882, 'postal' => '81111'],
            ]
        ],
        'Sumatera Utara' => [
            'lat' => 2.1154, 'lng' => 99.5451,
            'cities' => [
                'Kota Medan' => ['lat' => 3.5952, 'lng' => 98.6722, 'postal' => '20111'],
                'Kota Pematangsiantar' => ['lat' => 2.9610, 'lng' => 99.0682, 'postal' => '21111'],
                'Kota Binjai' => ['lat' => 3.6003, 'lng' => 98.4854, 'postal' => '20711'],
                'Kabupaten Deli Serdang' => ['lat' => 3.4167, 'lng' => 98.6667, 'postal' => '20511'],
            ]
        ],
        'Sumatera Barat' => [
            'lat' => -0.7399, 'lng' => 100.8000,
            'cities' => [
                'Kota Padang' => ['lat' => -0.9471, 'lng' => 100.4172, 'postal' => '25111'],
                'Kota Bukittinggi' => ['lat' => -0.3056, 'lng' => 100.3692, 'postal' => '26111'],
                'Kota Payakumbuh' => ['lat' => -0.2244, 'lng' => 100.6322, 'postal' => '26211'],
            ]
        ],
        'Riau' => [
            'lat' => 0.5071, 'lng' => 101.4478,
            'cities' => [
                'Kota Pekanbaru' => ['lat' => 0.5071, 'lng' => 101.4478, 'postal' => '28111'],
                'Kota Dumai' => ['lat' => 1.6667, 'lng' => 101.4500, 'postal' => '28811'],
            ]
        ],
        'Kepulauan Riau' => [
            'lat' => 3.9456, 'lng' => 108.1429,
            'cities' => [
                'Kota Batam' => ['lat' => 1.1301, 'lng' => 104.0529, 'postal' => '29411'],
                'Kota Tanjungpinang' => ['lat' => 0.9167, 'lng' => 104.4500, 'postal' => '29111'],
            ]
        ],
        'Sumatera Selatan' => [
            'lat' => -3.3194, 'lng' => 104.9144,
            'cities' => [
                'Kota Palembang' => ['lat' => -2.9761, 'lng' => 104.7754, 'postal' => '30111'],
                'Kota Lubuklinggau' => ['lat' => -3.2944, 'lng' => 102.8617, 'postal' => '31611'],
            ]
        ],
        'Lampung' => [
            'lat' => -4.5586, 'lng' => 105.4068,
            'cities' => [
                'Kota Bandar Lampung' => ['lat' => -5.4500, 'lng' => 105.2667, 'postal' => '35111'],
                'Kota Metro' => ['lat' => -5.1133, 'lng' => 105.3067, 'postal' => '34111'],
            ]
        ],
        'Kalimantan Barat' => [
            'lat' => -0.0263, 'lng' => 109.3425,
            'cities' => [
                'Kota Pontianak' => ['lat' => -0.0263, 'lng' => 109.3425, 'postal' => '78111'],
                'Kota Singkawang' => ['lat' => 0.9069, 'lng' => 108.9869, 'postal' => '79111'],
            ]
        ],
        'Kalimantan Timur' => [
            'lat' => 0.5387, 'lng' => 116.4194,
            'cities' => [
                'Kota Samarinda' => ['lat' => -0.5022, 'lng' => 117.1536, 'postal' => '75111'],
                'Kota Balikpapan' => ['lat' => -1.2379, 'lng' => 116.8289, 'postal' => '76111'],
                'Ibu Kota Nusantara (IKN)' => ['lat' => -0.9739, 'lng' => 116.7082, 'postal' => '77111'],
            ]
        ],
        'Kalimantan Selatan' => [
            'lat' => -3.0926, 'lng' => 115.2838,
            'cities' => [
                'Kota Banjarmasin' => ['lat' => -3.3194, 'lng' => 114.5908, 'postal' => '70111'],
                'Kota Banjarbaru' => ['lat' => -3.4400, 'lng' => 114.8300, 'postal' => '70711'],
            ]
        ],
        'Sulawesi Selatan' => [
            'lat' => -3.6687, 'lng' => 119.9740,
            'cities' => [
                'Kota Makassar' => ['lat' => -5.1477, 'lng' => 119.4327, 'postal' => '90111'],
                'Kota Parepare' => ['lat' => -4.0133, 'lng' => 119.6267, 'postal' => '91111'],
                'Kota Palopo' => ['lat' => -2.9944, 'lng' => 120.1969, 'postal' => '91911'],
            ]
        ],
        'Sulawesi Utara' => [
            'lat' => 0.6247, 'lng' => 123.9750,
            'cities' => [
                'Kota Manado' => ['lat' => 1.4748, 'lng' => 124.8421, 'postal' => '95111'],
                'Kota Tomohon' => ['lat' => 1.3283, 'lng' => 124.8394, 'postal' => '95411'],
            ]
        ],
        'Nusa Tenggara Barat' => [
            'lat' => -8.6529, 'lng' => 117.3616,
            'cities' => [
                'Kota Mataram' => ['lat' => -8.5833, 'lng' => 116.1167, 'postal' => '83111'],
                'Kota Bima' => ['lat' => -8.4667, 'lng' => 118.7167, 'postal' => '84111'],
            ]
        ],
        'Papua' => [
            'lat' => -4.2699, 'lng' => 138.0804,
            'cities' => [
                'Kota Jayapura' => ['lat' => -2.5337, 'lng' => 140.7181, 'postal' => '99111'],
            ]
        ],
    ];

    /**
     * Dapatkan semua daftar provinsi.
     */
    public static function getProvinces(): array
    {
        return array_keys(self::PROVINCES_DATA);
    }

    /**
     * Dapatkan kota-kota dalam suatu provinsi.
     */
    public static function getCities(string $province): array
    {
        return self::PROVINCES_DATA[$province]['cities'] ?? [];
    }

    /**
     * Cari koordinat pusat berdasarkan nama kota atau provinsi.
     */
    public static function getCoordinates(string $province = '', string $city = ''): array
    {
        if ($province && isset(self::PROVINCES_DATA[$province])) {
            if ($city && isset(self::PROVINCES_DATA[$province]['cities'][$city])) {
                return self::PROVINCES_DATA[$province]['cities'][$city];
            }
            return [
                'lat' => self::PROVINCES_DATA[$province]['lat'],
                'lng' => self::PROVINCES_DATA[$province]['lng'],
            ];
        }

        // Default Indonesia (Jakarta)
        return ['lat' => -6.2088, 'lng' => 106.8456];
    }
}
