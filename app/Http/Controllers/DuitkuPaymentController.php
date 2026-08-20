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
     * URL Endpoint Duitku
     */
    private string $baseUrl;
    private string $merchantCode;
    private string $apiKey;
    private string $callbackUrl;
    private string $returnUrl;

    public function __construct()
    {
        $env = config('services.duitku.env', env('DUITKU_ENV', 'sandbox'));
        $this->baseUrl = ($env === 'production')
            ? 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'
            : 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';

        $this->merchantCode = config('services.duitku.merchant_code', env('DUITKU_MERCHANT_CODE', 'DS34393'));
        $this->apiKey       = config('services.duitku.api_key', env('DUITKU_API_KEY', '72cf764c6dd4fbf92f134f39bde5dbe3'));
        $this->callbackUrl  = config('services.duitku.callback_url', env('DUITKU_CALLBACK_URL', url('/api/duitku/callback')));
        $this->returnUrl    = config('services.duitku.return_url', env('DUITKU_RETURN_URL', url('/payment/finish')));
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
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan ini sudah dibayar atau dibatalkan.',
            ], 422);
        }

        $order->load(['user', 'orderItems.product']);

        $paymentAmount   = (int) $order->total_amount;
        $merchantOrderId = $order->invoice_number; // e.g. INV-202608200001
        $paymentMethod   = $request->input('payment_method', ''); // Kosongkan jika ingin popup menampilkan semua channel
        $productDetails  = 'Pembayaran Pesanan #' . $merchantOrderId;
        $customerEmail   = $order->user->email ?? Auth::user()->email ?? 'customer@example.com';
        $customerPhone   = $order->user->phone ?? Auth::user()->phone ?? '081234567890';
        $customerName    = $order->user->name ?? Auth::user()->name ?? 'Customer';

        // Validasi batas minimal nominal transaksi Payment Gateway Duitku (Rp 10.000)
        if ($paymentAmount < 10000) {
            $msg = "Nominal tagihan (Rp " . number_format($paymentAmount, 0, ',', '.') . ") di bawah batas minimal Duitku Gateway (Rp 10.000). Silakan gunakan tab 'QRIS Instan', 'Virtual Account', atau 'Simulasi Bayar' untuk menyelesaikan pesanan ini.";
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // Perhitungan Signature MD5 Inquiry: md5(merchantCode + merchantOrderId + paymentAmount + apiKey)
        $signature = md5($this->merchantCode . $merchantOrderId . $paymentAmount . $this->apiKey);

        // Siapkan Item Details yang selalu sesuai dengan total paymentAmount
        $itemDetails = [
            [
                'name'     => substr('Pesanan #' . $merchantOrderId, 0, 50),
                'price'    => (int) $paymentAmount,
                'quantity' => 1,
            ],
        ];

        $payload = [
            'merchantCode'     => $this->merchantCode,
            'paymentAmount'    => $paymentAmount,
            'paymentMethod'    => $paymentMethod,
            'merchantOrderId'  => $merchantOrderId,
            'productDetails'   => $productDetails,
            'additionalParam'  => (string) $order->id,
            'merchantUserInfo' => (string) $order->user_id,
            'customerVaName'   => $customerName,
            'email'            => $customerEmail,
            'phoneNumber'      => $customerPhone,
            'itemDetails'      => $itemDetails,
            'callbackUrl'      => $this->callbackUrl,
            'returnUrl'        => $this->returnUrl,
            'signature'        => $signature,
            'expiryPeriod'     => 1440, // 24 Jam
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post($this->baseUrl, $payload);

            $result = $response->json();

            Log::info('Duitku Inquiry Response:', ['order' => $merchantOrderId, 'response' => $result]);

            if ($response->successful() && isset($result['statusCode']) && $result['statusCode'] === '00' && !empty($result['paymentUrl'])) {
                // Simpan reference Duitku ke database order
                $order->update([
                    'payment_reference' => $result['reference'] ?? null,
                ]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'status'         => 'success',
                        'reference'      => $result['reference'] ?? null,
                        'payment_url'    => $result['paymentUrl'],
                        'merchant_code'  => $this->merchantCode,
                        'merchant_order' => $merchantOrderId,
                        'amount'         => $paymentAmount,
                    ]);
                }

                // Redirect user ke Duitku Payment Page
                return redirect()->away($result['paymentUrl']);
            }

            $errorMessage = $result['statusMessage'] ?? 'Gagal menghubungi gateway Duitku Sandbox. Silakan gunakan tab QRIS Instan / Virtual Account.';
            Log::error('Duitku Inquiry Error:', ['error' => $result]);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], 400);
            }

            return back()->with('error', 'Pembayaran Duitku Gagal: ' . $errorMessage);

        } catch (\Exception $e) {
            Log::error('Duitku Exception:', ['message' => $e->getMessage()]);

            $errorMsg = 'Koneksi ke gateway Duitku Sandbox timeout atau sedang offline. Silakan gunakan tab QRIS Instan / Simulasi Bayar.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 500);
            }

            return back()->with('error', $errorMsg);
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
        $order = Order::where('invoice_number', $merchantOrderId)->first();

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
            $order = Order::where('invoice_number', $merchantOrderId)->first();
            if ($order) {
                if ($resultCode === '00') {
                    // Update status jika belum terupdate oleh webhook
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
