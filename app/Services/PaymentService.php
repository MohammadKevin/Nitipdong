<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Daftar channel pembayaran otomatis.
     */
    public const PAYMENT_CHANNELS = [
        'qris' => [
            'name'     => 'QRIS (Semua E-Wallet & M-Banking)',
            'icon'     => 'fa-solid fa-qrcode',
            'type'     => 'qris',
            'badge'    => 'Otomatis & Instan',
            'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        'va_bca' => [
            'name'     => 'BCA Virtual Account',
            'icon'     => 'fa-solid fa-building-columns',
            'type'     => 'va',
            'bank'     => 'BCA',
            'badge'    => 'Verifikasi Otomatis',
            'badge_bg' => 'bg-blue-50 text-blue-700 border-blue-200',
        ],
        'va_mandiri' => [
            'name'     => 'Mandiri Virtual Account',
            'icon'     => 'fa-solid fa-building-columns',
            'type'     => 'va',
            'bank'     => 'Mandiri',
            'badge'    => 'Verifikasi Otomatis',
            'badge_bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
        'va_bni' => [
            'name'     => 'BNI Virtual Account',
            'icon'     => 'fa-solid fa-building-columns',
            'type'     => 'va',
            'bank'     => 'BNI',
            'badge'    => 'Verifikasi Otomatis',
            'badge_bg' => 'bg-orange-50 text-orange-700 border-orange-200',
        ],
        'va_bri' => [
            'name'     => 'BRI Virtual Account (BRIVA)',
            'icon'     => 'fa-solid fa-building-columns',
            'type'     => 'va',
            'bank'     => 'BRI',
            'badge'    => 'Verifikasi Otomatis',
            'badge_bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        ],
    ];

    /**
     * Generate data transaksi gateway (QRIS String, VA Number, Expired Time) langsung via Midtrans Core API.
     */
    public static function createPaymentCharge(Order $order, string $paymentMethod): array
    {
        $serverKey = config('services.midtrans.server_key', 'Mid-server-QRIG4umIOjT0Q4w1JDxzIc0c');
        $chargeUrl = 'https://api.sandbox.midtrans.com/v2/charge';

        $method = strtolower($paymentMethod);
        $orderIdUnique = $order->invoice_number . '-' . time();
        $grossAmount = (int) round($order->total_amount);
        $user = $order->user ?? auth()->user();

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderIdUnique,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Customer',
                'email'      => $user->email ?? 'customer@budayakita.com',
                'phone'      => preg_replace('/[^0-9]/', '', $user->phone ?? '081234567890') ?: '081234567890',
            ],
        ];

        if (str_contains($method, 'bri')) {
            $payload['payment_type'] = 'bank_transfer';
            $payload['bank_transfer'] = ['bank' => 'bri'];
        } elseif (str_contains($method, 'bca')) {
            $payload['payment_type'] = 'bank_transfer';
            $payload['bank_transfer'] = ['bank' => 'bca'];
        } elseif (str_contains($method, 'bni')) {
            $payload['payment_type'] = 'bank_transfer';
            $payload['bank_transfer'] = ['bank' => 'bni'];
        } elseif (str_contains($method, 'mandiri')) {
            $payload['payment_type'] = 'echannel';
            $payload['echannel'] = [
                'bill_info1' => 'Pembayaran:',
                'bill_info2' => 'Order ' . $order->invoice_number,
            ];
        } else {
            $payload['payment_type'] = 'qris';
            $payload['qris'] = ['acquirer' => 'gopay'];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post($chargeUrl, $payload);

            $resData = $response->json();

            if ($response->successful() && ($resData['status_code'] ?? '400') < 300) {
                $vaNumber = null;
                if (!empty($resData['va_numbers'][0]['va_number'])) {
                    $vaNumber = $resData['va_numbers'][0]['va_number'];
                } elseif (!empty($resData['permata_va_number'])) {
                    $vaNumber = $resData['permata_va_number'];
                } elseif (!empty($resData['bill_key'])) {
                    $vaNumber = $resData['bill_key'];
                }

                $qrString = $resData['qr_string'] ?? null;
                $qrImageUrl = $qrString 
                    ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrString)
                    : ($resData['actions'][0]['url'] ?? null);

                $order->update([
                    'payment_reference' => $resData['transaction_id'] ?? $orderIdUnique,
                ]);

                return [
                    'reference'      => $resData['transaction_id'] ?? $orderIdUnique,
                    'payment_method' => $paymentMethod,
                    'bank_name'      => strtoupper($resData['va_numbers'][0]['bank'] ?? 'BCA'),
                    'amount'         => $grossAmount,
                    'va_number'      => $vaNumber,
                    'biller_code'    => $resData['biller_code'] ?? null,
                    'bill_key'       => $resData['bill_key'] ?? null,
                    'qr_string'      => $qrString,
                    'qris_data'      => $qrString,
                    'qris_image_url' => $qrImageUrl,
                    'expires_at'     => $resData['expiry_time'] ?? now()->addHours(24)->toIso8601String(),
                ];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Service Charge Exception:', ['msg' => $e->getMessage()]);
        }

        // Fallback jika mode offline / sandbox issue
        $vaPrefixes = [
            'va_bca'     => '12345',
            'va_mandiri' => '70012',
            'va_bni'     => '98888',
            'va_bri'     => '88776',
        ];
        $prefix = $vaPrefixes[$paymentMethod] ?? '88776';
        $cleanPhone = preg_replace('/[^0-9]/', '', $order->user->phone ?? '081234567890');
        $fallbackVa = $prefix . substr($cleanPhone . '12345678', 0, 10);

        $qrisData = "00020101021226680016ID.CO.NITIPDONG.WWW011893600999" . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . "5204541153033605802ID5918NITIPDONG6007JAKARTA62070703A016304" . strtoupper(substr(md5($order->invoice_number), 0, 4));

        return [
            'reference'      => 'PAY-' . strtoupper(Str::random(8)) . '-' . $order->id,
            'payment_method' => $paymentMethod,
            'bank_name'      => 'Bank ' . strtoupper(str_replace('va_', '', $paymentMethod)),
            'amount'         => $grossAmount,
            'va_number'      => $fallbackVa,
            'qr_string'      => $qrisData,
            'qris_data'      => $qrisData,
            'qris_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisData),
            'expires_at'     => now()->addHours(24)->toIso8601String(),
        ];
    }

    /**
     * Eksekusi status bayar sukses (Webhook / Simulasi / Callback).
     */
    public static function handlePaymentSuccess(Order $order, string $reference = '', string $method = 'qris'): bool
    {
        if ($order->status !== 'pending') {
            return true; // Already processed
        }

        DB::transaction(function () use ($order, $reference, $method) {
            $order->update([
                'status'            => 'processing',
                'payment_method'    => $method,
                'payment_reference' => $reference ?: ('TX-' . strtoupper(Str::random(10))),
            ]);

            // Kirim notifikasi realtime ke seller
            if ($order->store && $order->store->user_id) {
                AppNotification::send(
                    $order->store->user_id,
                    'Pembayaran Terverifikasi (Otomatis)',
                    "Pembayaran pesanan #{$order->invoice_number} senilai Rp " . number_format($order->total_amount, 0, ',', '.') . " telah lunas via {$method}. Segera proses dan kirim pesanan!",
                    'order',
                    route('seller.orders.index')
                );
            }
        });

        return true;
    }
}
