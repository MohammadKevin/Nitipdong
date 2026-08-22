<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * Get carousel promo banners for mobile home screen.
     */
    public function index(): JsonResponse
    {
        $banners = [
            [
                'id'          => 1,
                'title'       => 'Pesta Diskon & Promo 2026',
                'subtitle'    => 'Belanja Praktis & Titip Beli dari Toko Terpercaya',
                'badge'       => 'PROMO TERBAIK',
                'image_url'   => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=800&auto=format&fit=crop',
                'target_url'  => '/products',
                'button_text' => 'Belanja Sekarang',
            ],
            [
                'id'          => 2,
                'title'       => 'Official Store & Verified Jastip',
                'subtitle'    => 'Garansi 100% Produk Asli & Jaminan Uang Kembali',
                'badge'       => '100% ORIGINAL',
                'image_url'   => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop',
                'target_url'  => '/products?official=1',
                'button_text' => 'Lihat Toko Resmi',
            ],
            [
                'id'          => 3,
                'title'       => 'Gratis Ongkir XTRA Seluruh Indonesia',
                'subtitle'    => 'Klaim Voucher Belanja Ekstra Ongkir Rp0 Setiap Hari',
                'badge'       => 'GRATIS ONGKIR',
                'image_url'   => 'https://images.unsplash.com/photo-1556742049-0a67e557b683?q=80&w=800&auto=format&fit=crop',
                'target_url'  => '/customer/vouchers',
                'button_text' => 'Klaim Kupon',
            ],
        ];

        return response()->json([
            'success' => true,
            'data'    => $banners,
        ]);
    }
}
