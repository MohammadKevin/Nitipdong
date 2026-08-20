<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuPaymentController extends Controller
{
    /**
     * URL Endpoint Duitku Sandbox
     */
    private string $baseUrl;
    private string $merchantCode;
    private string $apiKey;
    private string $callbackUrl;
    private string $returnUrl;

    public function __construct()
    {
        $this->baseUrl      = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';
        $this->merchantCode = env('DUITKU_MERCHANT_CODE', 'DS34393');
        $this->apiKey       = env('DUITKU_API_KEY', '72cf764c6dd4fbf92f134f39bde5dbe3');
        $this->callbackUrl  = env('DUITKU_CALLBACK_URL', 'https://budayakita.com/api/duitku/callback');
        $this->returnUrl    = env('DUITKU_RETURN_URL', 'https://budayakita.com/payment/finish');
    }

    /**
     * 1. Request Pembuatan Transaksi ke Duitku Sandbox (Inquiry)
     */
    public function createInvoice(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        // Validasi kepemilikan pesanan
        if ($order->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Akses tidak sah untuk pesanan ini.');
        }

        if ($order->status !== 'pending') {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Pesanan ini sudah dibayar atau dibatalkan.',
                ], 422);
            }
            return redirect()->route('customer.dashboard')->with('info', 'Pesanan ini sudah dibayar atau dibatalkan.');
        }

        $order->load(['user', 'orderItems.product']);

        $paymentAmount   = (int) round($order->total_amount);
        $merchantOrderId = (string) ($order->order_number ?? $order->invoice_number ?? $order->id);
        $customerEmail   = filter_var($order->user->email ?? Auth::user()->email ?? 'customer@example.com', FILTER_VALIDATE_EMAIL) ?: 'customer@example.com';
        $customerPhone   = preg_replace('/[^0-9]/', '', $order->user->phone ?? Auth::user()->phone ?? '081234567890') ?: '081234567890';
        $customerName    = preg_replace('/[^a-zA-Z0-9\s]/', '', $order->user->name ?? Auth::user()->name ?? 'Customer') ?: 'Customer';

        // Rumus Signature MD5: md5(merchantCode + merchantOrderId + paymentAmount + apiKey)
        $signature = md5($this->merchantCode . $merchantOrderId . $paymentAmount . $this->apiKey);

        $payload = [
            'merchantCode'     => $this->merchantCode,
            'paymentAmount'    => $paymentAmount,
            'merchantOrderId'  => $merchantOrderId,
            'productDetails'   => 'Pembayaran Pesanan #' . $merchantOrderId,
            'email'            => $customerEmail,
            'phoneNumber'      => $customerPhone,
            'customerVaName'   => $customerName,
            'callbackUrl'      => $this->callbackUrl,
            'returnUrl'        => $this->returnUrl,
            'signature'        => $signature,
            'expiryPeriod'     => 1440,
        ];

        // Jangan kirim parameter paymentMethod jika kosong
        if (!empty($request->input('payment_method'))) {
            $payload['paymentMethod'] = $request->input('payment_method');
        }

        try {
            $response = Http::asJson()
                ->withoutVerifying()
                ->timeout(20)
                ->post($this->baseUrl, $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['statusCode']) && $result['statusCode'] === '00' && !empty($result['paymentUrl'])) {
                $order->update([
                    'payment_reference' => $result['reference'] ?? null,
                ]);

                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status'     => 'success',
                        'paymentUrl' => $result['paymentUrl'],
                    ]);
                }

                return redirect()->away($result['paymentUrl']);
            }

            Log::error('Duitku Inquiry Error:', [
                'order'    => $merchantOrderId,
                'response' => $result,
                'raw_body' => $response->body(),
                'status'   => $response->status(),
            ]);

            $errorMessage = $result['statusMessage'] ?? $result['message'] ?? 'Ditolak Duitku';

            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $errorMessage,
                ], 400);
            }

            return redirect()->route('customer.order.payment', $order)->with('error', 'Pembayaran Duitku Gagal: ' . $errorMessage);

        } catch (\Exception $e) {
            Log::error('Duitku Exception:', [
                'order'   => $merchantOrderId,
                'message' => $e->getMessage(),
            ]);

            $errorMsg = 'Koneksi ke gateway Duitku Sandbox terjadi kendala: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $errorMsg,
                ], 500);
            }

            return redirect()->route('customer.order.payment', $order)->with('error', $errorMsg);
        }
    }

    /**
     * 2. Webhook / Callback Handler dari Duitku (POST)
     */
    public function handleCallback(Request $request): JsonResponse
    {
        Log::info('Duitku Callback Received:', $request->all());

        $merchantCode    = $request->input('merchantCode');
        $amount          = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $signature       = $request->input('signature');
        $resultCode      = $request->input('resultCode'); // '00' = Success
        $reference       = $request->input('reference');
        $paymentCode     = $request->input('paymentCode', 'duitku');

        if (empty($merchantCode) || empty($amount) || empty($merchantOrderId) || empty($signature)) {
            Log::warning('Duitku Callback: Missing required fields.');
            return response()->json(['status' => 'Bad Parameter'], 400);
        }

        // Perhitungan Signature MD5 Callback: md5(merchantCode + amount + merchantOrderId + apiKey)
        $expectedSignature = md5($merchantCode . $amount . $merchantOrderId . $this->apiKey);

        if ($signature !== $expectedSignature) {
            Log::error('Duitku Callback: Invalid Signature!', [
                'received' => $signature,
                'expected' => $expectedSignature,
            ]);
            return response()->json(['status' => 'Invalid Signature'], 400);
        }

        // Cari pesanan berdasarkan invoice_number / merchantOrderId
        $order = Order::where('invoice_number', $merchantOrderId)
            ->orWhere('id', $merchantOrderId)
            ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$merchantOrderId])
            ->first();

        if (!$order) {
            Log::error("Duitku Callback: Order #{$merchantOrderId} not found.");
            return response()->json(['status' => 'Order Not Found'], 404);
        }

        // Jika status sukses '00'
        if ($resultCode === '00') {
            PaymentService::handlePaymentSuccess($order, $reference ?: ('DK-' . $merchantOrderId), 'Duitku (' . $paymentCode . ')');
            Log::info("Duitku Callback: Order #{$merchantOrderId} marked as PAID.");

            return response()->json(['status' => 'SUCCESS'], 200);
        }

        // Jika status gagal / expired
        Log::warning("Duitku Callback: Order #{$merchantOrderId} payment failed with code: {$resultCode}");
        return response()->json(['status' => 'FAILED / EXPIRED'], 200);
    }

    /**
     * 3. Return URL Redirect Handler setelah Pembeli Selesai Bayar
     */
    public function handleReturn(Request $request): RedirectResponse
    {
        $merchantOrderId = $request->input('merchantOrderId');
        $resultCode      = $request->input('resultCode');
        $reference       = $request->input('reference');

        Log::info('Duitku Return URL Triggered:', $request->all());

        if ($merchantOrderId) {
            $order = Order::where('invoice_number', $merchantOrderId)
                ->orWhere('id', $merchantOrderId)
                ->orWhereRaw("REPLACE(invoice_number, '-', '') = ?", [$merchantOrderId])
                ->first();

            if ($order) {
                if ($resultCode === '00') {
                    if ($order->status === 'pending') {
                        PaymentService::handlePaymentSuccess($order, $reference ?: ('DK-' . $merchantOrderId), 'Duitku');
                    }

                    return redirect()->route('customer.dashboard')
                        ->with('success', "Pembayaran pesanan #{$order->invoice_number} berhasil! Pesanan Anda sedang disiapkan oleh toko.");
                }

                return redirect()->route('customer.order.payment', $order)
                    ->with('error', "Pembayaran belum diselesaikan atau dibatalkan. Silakan coba bayar kembali.");
            }
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Transaksi Duitku Anda telah selesai diproses.');
    }
}
