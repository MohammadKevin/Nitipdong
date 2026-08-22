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
        'manual_transfer' => [
            'name'     => 'Transfer Bank Manual (Struk)',
            'icon'     => 'fa-solid fa-receipt',
            'type'     => 'manual',
            'badge'    => 'Verifikasi Struk',
            'badge_bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        ],
    ];

    /**
     * Generate data transaksi gateway (QRIS String, VA Number, Expired Time).
     */
    public static function createPaymentCharge(Order $order, string $paymentMethod): array
    {
        $orderIdPad = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $total = (int) $order->total_amount;

        $reference = 'PAY-' . strtoupper(Str::random(8)) . '-' . $order->id;

        $vaPrefixes = [
            'va_bca'     => '8801',
            'va_mandiri' => '8802',
            'va_bni'     => '8803',
            'va_bri'     => '8804',
        ];

        $bankNames = [
            'va_bca'     => 'Bank BCA',
            'va_mandiri' => 'Bank Mandiri',
            'va_bni'     => 'Bank BNI',
            'va_bri'     => 'Bank BRI (BRIVA)',
        ];

        $vaNumber = null;
        if (isset($vaPrefixes[$paymentMethod])) {
            $vaNumber = $vaPrefixes[$paymentMethod] . '99' . $orderIdPad;
        } else {
            $vaNumber = '880199' . $orderIdPad;
        }

        // Generate QR code data payload
        $qrisData = "00020101021226680016ID.CO.NITIPDONG.WWW011893600999" . $orderIdPad . "5204541153033605802ID5918NITIPDONG INDONESIA6007JAKARTA62070703A016304" . strtoupper(substr(md5($order->invoice_number), 0, 4));

        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisData);

        return [
            'reference'      => $reference,
            'payment_method' => $paymentMethod,
            'bank_name'      => $bankNames[$paymentMethod] ?? 'Bank BCA',
            'amount'         => $total,
            'va_number'      => $vaNumber,
            'qris_data'      => $qrisData,
            'qris_image_url' => $qrImageUrl,
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
