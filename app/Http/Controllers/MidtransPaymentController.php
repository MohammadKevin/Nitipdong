<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Voucher;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentController extends Controller
{
    private const SNAP_URL = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

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
        $serverKey = config('services.midtrans.server_key');
        $clientKey = config('services.midtrans.client_key');

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
        $response = Http::withBasicAuth($serverKey, '')
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
                'client_key' => $clientKey,
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
            $serverKey         = config('services.midtrans.server_key');

            if (empty($orderId)) {
                return response()->json(['status' => 'error', 'message' => 'Order ID is missing.'], 400);
            }

            // Validasi Signature Key: SHA512(order_id + status_code + gross_amount + ServerKey)
            if (!empty($signatureKey) && !empty($statusCode) && !empty($grossAmount) && !empty($serverKey)) {
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
                if ($signatureKey !== $expectedSignature) {
                    Log::warning('Midtrans Invalid Signature Key', [
                        'received' => $signatureKey,
                        'expected' => $expectedSignature,
                    ]);
                    return response()->json(['message' => 'Unauthorized'], 403);
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
                Log::warning("Midtrans Notification: Order #{$orderId} not found in database. Returning 200 OK to acknowledge.");
                return response()->json(['status' => 'ok', 'message' => 'Notification received and acknowledged.'], 200);
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
                if ($order->status !== 'cancelled') {
                    DB::transaction(function () use ($order, $transactionStatus) {
                        $order->update(['status' => 'cancelled']);

                        // Restore stok produk
                        foreach ($order->orderItems as $item) {
                            if ($item->product) {
                                $item->product->increment('stock', $item->quantity);
                                $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                            }
                        }

                        // Restore quota voucher jika ada
                        if ($order->voucher_code) {
                            $voucher = Voucher::where('code', $order->voucher_code)->first();
                            if ($voucher) {
                                $voucher->increment('quota');
                            }
                        }

                        // Kirim notifikasi ke pembeli
                        AppNotification::send(
                            $order->user_id,
                            'Pesanan Dibatalkan (Pembayaran Gagal/Kedaluwarsa)',
                            "Pesanan #{$order->invoice_number} telah dibatalkan karena pembayaran {$transactionStatus}. Stok produk telah dikembalikan.",
                            'order',
                            route('customer.dashboard')
                        );

                        // Kirim notifikasi ke penjual
                        if ($order->store && $order->store->user_id) {
                            AppNotification::send(
                                $order->store->user_id,
                                'Pesanan Dibatalkan Sistem',
                                "Pesanan #{$order->invoice_number} dibatalkan otomatis oleh gateway pembayaran ({$transactionStatus}). Stok produk telah dipulihkan.",
                                'order',
                                route('seller.orders.index')
                            );
                        }
                    });
                }
                Log::info("Midtrans: Order #{$orderId} {$transactionStatus} and stock restored.");
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Exception:', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

