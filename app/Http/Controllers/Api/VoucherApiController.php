<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherApiController extends Controller
{
    /**
     * Get active vouchers available for mobile customers.
     */
    public function available(Request $request): JsonResponse
    {
        $vouchers = Voucher::active()->with('store')->latest()->get();

        if ($vouchers->isEmpty()) {
            // Seed / Fallback if empty in dev
            $vouchers = collect([
                [
                    'id'               => 1,
                    'code'             => 'NITIPHEMAT20',
                    'name'             => 'Diskon Spesial Pengguna Baru',
                    'description'      => 'Potongan 20% untuk semua kategori produk tanpa minimum transaksi!',
                    'type'             => 'percent',
                    'amount'           => 20.0,
                    'min_spend'        => 50000.0,
                    'max_discount'     => 50000.0,
                    'formatted_discount'=> '20% OFF',
                    'expires_at'       => now()->addMonths(1)->format('d M Y'),
                    'quota'            => 150,
                    'badge'            => 'Paling Populer 🔥',
                    'is_valid'         => true,
                ],
                [
                    'id'               => 2,
                    'code'             => 'ONGKIRGRATIS',
                    'name'             => 'Gratis Ongkir Seluruh Indonesia',
                    'description'      => 'Potongan ongkos kirim hingga Rp 25.000 dengan min. belanja Rp 75.000.',
                    'type'             => 'fixed',
                    'amount'           => 25000.0,
                    'min_spend'        => 75000.0,
                    'max_discount'     => 25000.0,
                    'formatted_discount'=> 'Potongan Rp 25.000',
                    'expires_at'       => now()->addMonths(2)->format('d M Y'),
                    'quota'            => 300,
                    'badge'            => 'Gratis Ongkir 🚚',
                    'is_valid'         => true,
                ],
                [
                    'id'               => 3,
                    'code'             => 'GAJIANSERU50',
                    'name'             => 'Cashback Flash Sale Gajian',
                    'description'      => 'Potongan langsung Rp 50.000 untuk transaksi minimal Rp 200.000.',
                    'type'             => 'fixed',
                    'amount'           => 50000.0,
                    'min_spend'        => 200000.0,
                    'max_discount'     => 50000.0,
                    'formatted_discount'=> 'Potongan Rp 50.000',
                    'expires_at'       => now()->addWeeks(3)->format('d M Y'),
                    'quota'            => 80,
                    'badge'            => 'Spesial Gajian 🎁',
                    'is_valid'         => true,
                ],
            ]);

            return response()->json([
                'success' => true,
                'data'    => $vouchers,
            ]);
        }

        $formatted = $vouchers->map(function ($v) {
            $formattedDiscount = $v->type === 'percent' 
                ? "{$v->amount}% OFF" 
                : "Potongan Rp " . number_format($v->amount, 0, ',', '.');

            return [
                'id'                => $v->id,
                'code'              => $v->code,
                'name'              => $v->name,
                'description'       => $v->description ?? "Diskon {$formattedDiscount} dengan minimal belanja Rp " . number_format($v->min_spend, 0, ',', '.'),
                'type'              => $v->type,
                'amount'            => (float) $v->amount,
                'min_spend'         => (float) $v->min_spend,
                'max_discount'      => (float) $v->max_discount,
                'formatted_discount'=> $formattedDiscount,
                'expires_at'        => $v->expires_at ? $v->expires_at->format('d M Y') : 'Berlaku Selamanya',
                'quota'             => (int) $v->quota,
                'badge'             => $v->type === 'percent' ? 'Diskon Persen ⚡' : 'Potongan Langsung 🎟️',
                'is_valid'          => true,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }
}
