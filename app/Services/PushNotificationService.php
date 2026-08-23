<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Kirim Notifikasi Perubahan Status Pesanan ke Pembeli / Penjual / Kurir
     */
    public static function sendOrderStatusNotification(Order $order, string $status, ?string $customMessage = null): bool
    {
        $user = $order->user;
        if (!$user) {
            return false;
        }

        $titleMap = [
            'paid'       => 'Pembayaran Berhasil Diterima! 💳',
            'processing' => 'Pesanan Sedang Dikemas Toko 📦',
            'shipped'    => 'Paket Sedang Diantar Kurir 🚚',
            'completed'  => 'Pesanan Telah Tiba & Selesai! 🎉',
            'cancelled'  => 'Pesanan Telah Dibatalkan ❌',
        ];

        $messageMap = [
            'paid'       => "Pembayaran untuk pesanan #{$order->invoice_number} telah terverifikasi. Penjual akan segera memproses barang.",
            'processing' => "Pesanan #{$order->invoice_number} sedang disiapkan dan dikemas oleh toko {$order->store?->name}.",
            'shipped'    => "Paket #{$order->invoice_number} telah diserahkan ke kurir ({$order->shipping_courier}) dan sedang dalam perjalanan ke alamat Anda.",
            'completed'  => "Paket #{$order->invoice_number} telah berhasil diterima. Terima kasih telah berbelanja di NitipDong!",
            'cancelled'  => "Pesanan #{$order->invoice_number} telah dibatalkan.",
        ];

        $title = $titleMap[$status] ?? 'Update Status Pesanan 🔔';
        $body  = $customMessage ?: ($messageMap[$status] ?? "Pesanan #{$order->invoice_number} telah diperbarui ke status: {$status}");

        return self::sendToUser($user, $title, $body, [
            'type'            => 'order_status_update',
            'order_id'        => (string) $order->id,
            'invoice_number'  => (string) $order->invoice_number,
            'status'          => (string) $status,
            'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
        ]);
    }

    /**
     * Kirim Notifikasi ke Device Pengguna via FCM
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        // Simpan juga ke log notifikasi sistem
        Log::info("Push Notification dispatched to User #{$user->id} ({$user->name}): {$title} - {$body}");

        $fcmServerKey = env('FCM_SERVER_KEY');
        $deviceToken  = $user->fcm_token ?? null;

        if (!$fcmServerKey || !$deviceToken) {
            // Jika token FCM belum disetup di .env, kita catat di log dan tetap return true agar flow order tidak crash
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $fcmServerKey,
                'Content-Type'  => 'application/json',
            ])->timeout(5)->post('https://fcm.googleapis.com/fcm/send', [
                'to'           => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data'         => $data,
                'priority'     => 'high',
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM Push Notification Exception: ' . $e->getMessage());
            return false;
        }
    }
}
