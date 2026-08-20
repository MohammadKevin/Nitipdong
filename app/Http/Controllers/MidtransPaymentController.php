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
    private string $serverKey;
    private string $clientKey;
    private bool $isProduction;
    private string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey    = config('services.midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c');
        $this->clientKey    = config('services.midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', 'Mid-client-nNuy0AuFjI35ym6k');
        $this->isProduction = false; // Kunci ke Sandbox
        $this->snapApiUrl   = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        // Konfigurasi Midtrans SDK jika kelas tersedia
        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey    = $this->serverKey;
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;
        }
    }

    /**
     * 1. Generate Midtrans Snap Token
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

        $order->load(['user', 'orderItems.product']);

        $serverKey = config('services.midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', 'Mid-server-ORIG4umIOjT0Q4w1JDxzlc0c');
        $clientKey = config('services.midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', 'Mid-client-nNuy0AuFjI35ym6k');

        // Setup ulang config Midtrans memastikan tidak null
        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey    = $serverKey;
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;
        }

        $grossAmount = (int) round($order->total_amount);
        // Pastikan order_id unik dengan menambahkan timestamp agar tidak ditolak Midtrans jika sudah pernah dicoba
        $orderNumber = $order->invoice_number ?: ('ORDER-' . $order->id);
        $orderId     = $orderNumber . '-' . time();

        $customer = $order->user ?? Auth::user();
        $customerDetails = [
            'first_name' => $customer->name ?? 'Pelanggan SakserShop',
            'email'      => filter_var($customer->email ?? 'customer@budayakita.com', FILTER_VALIDATE_EMAIL) ?: 'customer@budayakita.com',
            'phone'      => preg_replace('/[^0-9]/', '', $customer->phone ?? '081234567890') ?: '081234567890',
        ];

        $itemDetails = [];
        foreach ($order->orderItems as $item) {
            $itemDetails[] = [
                'id'       => (string) $item->product_id,
                'price'    => (int) round($item->price),
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product->name ?? 'Produk SakserShop', 0, 50),
            ];
        }

        if (($order->shipping_cost ?? 0) > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) round($order->shipping_cost),
                'quantity' => 1,
                'name'     => 'Biaya Pengiriman Ekspedisi',
            ];
        }

        if (($order->discount_amount ?? 0) > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -((int) round($order->discount_amount)),
                'quantity' => 1,
                'name'     => 'Diskon Voucher Promo',
            ];
        }

        // Pastikan total item_details persis sama dengan gross_amount
        $itemsSum = array_sum(array_map(fn($it) => $it['price'] * $it['quantity'], $itemDetails));
        if ($itemsSum !== $grossAmount) {
            $itemDetails = [
                [
                    'id'       => 'ORDER-' . $order->id,
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => substr('Pembayaran Pesanan #' . $orderNumber, 0, 50),
                ]
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details'        => $itemDetails,
            'customer_details'    => $customerDetails,
        ];

        try {
            $snapToken = null;

            // Coba generate via Midtrans SDK
            if (class_exists(\Midtrans\Snap::class)) {
                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                } catch (\Exception $sdkEx) {
                    Log::warning('Midtrans SDK getSnapToken exception, trying direct HTTP:', ['message' => $sdkEx->getMessage()]);
                }
            }

            // Fallback direct HTTP API ke Midtrans Snap endpoint
            if (empty($snapToken)) {
                $response = Http::withBasicAuth($serverKey, '')
                    ->withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])
                    ->post($this->snapApiUrl, $params);

                if ($response->successful()) {
                    $result = $response->json();
                    $snapToken = $result['token'] ?? null;
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
                $order->update([
                    'payment_reference' => $snapToken,
                ]);

                return response()->json([
                    'status'     => 'success',
                    'snap_token' => $snapToken,
                    'token'      => $snapToken,
                    'client_key' => $clientKey,
                    'order_id'   => $orderId,
                    'amount'     => $grossAmount,
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
                $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
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
