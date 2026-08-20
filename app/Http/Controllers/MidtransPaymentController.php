<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MidtransPaymentController extends Controller
{
    private string $serverKey;
    private string $clientKey;

    public function __construct()
    {
        $this->serverKey = 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c';
        $this->clientKey = 'Mid-client-nNuy0AuFjI35ym6k';

        // Konfigurasi Midtrans SDK secara eksplisit ke Sandbox
        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey    = $this->serverKey;
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;
        }
    }

    /**
     * 1. Force Generate Fresh Midtrans Snap Token
     */
    public function getSnapToken(Request $request, Order $order): JsonResponse
    {
        // Validasi kepemilikan pesanan
        if ($order->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses tidak sah untuk pesanan ini.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Pesanan ini sudah dibayar atau dibatalkan.'], 422);
        }

        $order->load(['user']);

        // Set konfigurasi Midtrans secara eksplisit
        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey    = 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c';
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;
        }

        // Buat order_id yang selalu unik dengan timestamp agar tidak ditolak Midtrans
        $orderNumber = $order->order_number ?? $order->invoice_number ?? ('ORDER-' . $order->id);
        $orderId     = $orderNumber . '-' . time();
        $grossAmount = (int) round($order->total_amount);

        $customer = auth()->user() ?? $order->user;
        $customerDetails = [
            'first_name' => $customer->name ?? 'Customer',
            'email'      => filter_var($customer->email ?? 'customer@budayakita.com', FILTER_VALIDATE_EMAIL) ?: 'customer@budayakita.com',
            'phone'      => preg_replace('/[^0-9]/', '', $customer->phone ?? '081234567890') ?: '081234567890',
        ];

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details'    => $customerDetails,
        ];

        try {
            $snapToken = null;

            // 1. Coba generate via Midtrans SDK
            if (class_exists(\Midtrans\Snap::class)) {
                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                } catch (\Exception $sdkEx) {
                    Log::warning('Midtrans SDK exception, falling back to direct HTTP API:', ['message' => $sdkEx->getMessage()]);
                }
            }

            // 2. Fallback direct HTTP API ke Midtrans Snap Sandbox
            if (empty($snapToken)) {
                $response = Http::withBasicAuth('Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c', '')
                    ->withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])
                    ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

                if ($response->successful()) {
                    $snapToken = $response->json('token');
                } else {
                    Log::error('Midtrans HTTP API Error:', [
                        'order'    => $orderId,
                        'status'   => $response->status(),
                        'response' => $response->json(),
                        'body'     => $response->body()
                    ]);

                    return response()->json([
                        'status'  => 'error',
                        'message' => $response->json('error_messages.0') ?? 'Gagal membuat Snap Token Midtrans: ' . $response->body()
                    ], 400);
                }
            }

            if (!empty($snapToken)) {
                // Simpan snap_token baru ke order
                try {
                    $order->update([
                        'payment_reference' => $snapToken,
                        'snap_token'        => $snapToken,
                    ]);
                } catch (\Exception $dbEx) {
                    $order->update(['payment_reference' => $snapToken]);
                }

                return response()->json([
                    'status'     => 'success',
                    'snap_token' => $snapToken,
                    'token'      => $snapToken,
                    'order_id'   => $orderId,
                    'client_key' => 'Mid-client-nNuy0AuFjI35ym6k',
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Token pembayaran tidak berhasil diperoleh dari gateway Midtrans.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Midtrans Exception:', [
                'order'   => $orderId,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghubungi gateway Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. Webhook / Notification Handler dari Midtrans (POST /api/midtrans/notification)
     */
    public function handleNotification(Request $request): JsonResponse
    {
        Log::info('Midtrans Notification Received:', $request->all());

        try {
            $transactionStatus = $request->input('transaction_status');
            $type              = $request->input('payment_type', 'midtrans');
            $orderId           = $request->input('order_id');
            $fraudStatus       = $request->input('fraud_status');
            $signatureKey      = $request->input('signature_key');
            $statusCode        = $request->input('status_code');
            $grossAmount       = $request->input('gross_amount');

            // Coba via Midtrans Notification class jika ada
            if (class_exists(\Midtrans\Notification::class)) {
                try {
                    \Midtrans\Config::$serverKey = 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c';
                    \Midtrans\Config::$isProduction = false;
                    $notif = new \Midtrans\Notification();
                    $transactionStatus = $notif->transaction_status ?? $transactionStatus;
                    $type              = $notif->payment_type ?? $type;
                    $orderId           = $notif->order_id ?? $orderId;
                    $fraudStatus       = $notif->fraud_status ?? $fraudStatus;
                } catch (\Exception $notifEx) {
                    Log::warning('Midtrans Notification parsing exception:', ['msg' => $notifEx->getMessage()]);
                }
            }

            if (empty($orderId)) {
                return response()->json(['status' => 'error', 'message' => 'Order ID is missing.'], 400);
            }

            // Validasi Signature Key jika signatureKey tersedia: SHA512(order_id + status_code + gross_amount + ServerKey)
            if (!empty($signatureKey) && !empty($statusCode) && !empty($grossAmount)) {
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c');
                if ($signatureKey !== $expectedSignature) {
                    Log::warning('Midtrans Invalid Signature Key', [
                        'received' => $signatureKey,
                        'expected' => $expectedSignature
                    ]);
                }
            }

            // Cari pesanan asli jika orderId memiliki suffix timestamp
            $cleanOrderId = preg_replace('/-\d{10,}$/', '', $orderId);

            $order = Order::where('invoice_number', $orderId)
                ->orWhere('invoice_number', $cleanOrderId)
                ->orWhere('id', $orderId)
                ->orWhere('id', $cleanOrderId)
                ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$orderId])
                ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$cleanOrderId])
                ->first();

            if (!$order) {
                Log::error("Midtrans Notification: Order #{$orderId} not found.");
                return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
            }

            // Status settlement atau capture (dengan fraud accept)
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    PaymentService::handlePaymentSuccess($order, 'MID-' . $orderId, 'Midtrans (' . $type . ')');
                    Log::info("Midtrans: Order #{$orderId} marked as PAID (capture accept).");
                }
            } elseif ($transactionStatus === 'settlement') {
                PaymentService::handlePaymentSuccess($order, 'MID-' . $orderId, 'Midtrans (' . $type . ')');
                Log::info("Midtrans: Order #{$orderId} marked as PAID (settlement).");
            } elseif ($transactionStatus === 'pending') {
                $order->update(['status' => 'pending']);
                Log::info("Midtrans: Order #{$orderId} status is PENDING.");
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update(['status' => 'cancelled']);
                Log::info("Midtrans: Order #{$orderId} status is {$transactionStatus}.");
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Exception:', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
