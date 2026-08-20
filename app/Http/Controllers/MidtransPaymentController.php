<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentController extends Controller
{
    private const SERVER_KEY = 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c';
    private const CLIENT_KEY = 'Mid-client-nNuy0AuFjI35ym6k';
    private const SNAP_URL   = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    /**
     * Generate Midtrans Snap Token via direct HTTP (tanpa SDK agar tidak terkena bug config cache).
     */
    public function getSnapToken(Request $request, Order $order): JsonResponse
    {
        // Validasi kepemilikan pesanan
        if ($order->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses tidak sah.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Pesanan ini sudah dibayar atau dibatalkan.'], 422);
        }

        // Buat order_id unik dengan timestamp agar tidak ditolak Midtrans jika sebelumnya pernah dicoba
        $orderNumber = $order->order_number ?? $order->invoice_number ?? ('ORDER-' . $order->id);
        $orderId     = $orderNumber . '-' . time();
        $grossAmount = (int) round($order->total_amount);

        $user = auth()->user();

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Customer',
                'email'      => $user->email ?? 'customer@budayakita.com',
                'phone'      => preg_replace('/[^0-9]/', '', $user->phone ?? '081234567890') ?: '081234567890',
            ],
        ];

        // Direct HTTP call ke Midtrans Snap Sandbox — tanpa pakai SDK agar konfigurasi pasti benar
        $response = Http::withBasicAuth(self::SERVER_KEY, '')
            ->withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(20)
            ->post(self::SNAP_URL, $payload);

        if ($response->successful()) {
            $snapToken = $response->json('token');

            try {
                $order->update([
                    'payment_reference' => $snapToken,
                    'snap_token'        => $snapToken,
                ]);
            } catch (\Exception $e) {
                $order->update(['payment_reference' => $snapToken]);
            }

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'client_key' => self::CLIENT_KEY,
                'order_id'   => $orderId,
            ]);
        }

        $errorBody     = $response->json();
        $errorMessages = $errorBody['error_messages'] ?? null;
        $errorMessage  = is_array($errorMessages) ? ($errorMessages[0] ?? 'Gagal membuat transaksi Midtrans.') : ($errorBody['message'] ?? 'Gagal membuat transaksi Midtrans.');

        Log::error('Midtrans Snap Error', [
            'order'   => $orderId,
            'status'  => $response->status(),
            'body'    => $response->body(),
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => $errorMessage,
        ], 400);
    }

    /**
     * Webhook / Notification Handler dari Midtrans.
     * Endpoint: POST /api/midtrans/notification (bebas CSRF)
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

            if (empty($orderId)) {
                return response()->json(['status' => 'error', 'message' => 'Order ID is missing.'], 400);
            }

            // Validasi Signature Key: SHA512(order_id + status_code + gross_amount + ServerKey)
            if (!empty($signatureKey) && !empty($statusCode) && !empty($grossAmount)) {
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . self::SERVER_KEY);
                if ($signatureKey !== $expectedSignature) {
                    Log::warning('Midtrans Invalid Signature Key', [
                        'received' => $signatureKey,
                        'expected' => $expectedSignature,
                    ]);
                }
            }

            // Cari pesanan asli (orderId bisa memiliki suffix timestamp '-1234567890')
            $cleanOrderId = preg_replace('/-\d{10,}$/', '', $orderId);

            $order = Order::where('invoice_number', $orderId)
                ->orWhere('invoice_number', $cleanOrderId)
                ->orWhere('id', $orderId)
                ->orWhere('id', $cleanOrderId)
                ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$orderId])
                ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$cleanOrderId])
                ->first();

            if (!$order) {
                Log::error("Midtrans: Order #{$orderId} not found.");
                return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
            }

            if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                PaymentService::handlePaymentSuccess($order, 'MID-' . $orderId, 'Midtrans (' . $type . ')');
                Log::info("Midtrans: Order #{$orderId} PAID (capture + accept).");
            } elseif ($transactionStatus === 'settlement') {
                PaymentService::handlePaymentSuccess($order, 'MID-' . $orderId, 'Midtrans (' . $type . ')');
                Log::info("Midtrans: Order #{$orderId} PAID (settlement).");
            } elseif ($transactionStatus === 'pending') {
                $order->update(['status' => 'pending']);
                Log::info("Midtrans: Order #{$orderId} PENDING.");
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update(['status' => 'cancelled']);
                Log::info("Midtrans: Order #{$orderId} {$transactionStatus}.");
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Exception:', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
