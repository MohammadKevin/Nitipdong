<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    private const SANDBOX_BASE_URL = 'https://api.sandbox.midtrans.com';
    private const PRODUCTION_BASE_URL = 'https://api.midtrans.com';

    /**
     * Webhook Notification Handler (Midtrans / Payment Gateway).
     * Menerima notifikasi real-time dari Midtrans tentang perubahan status pembayaran.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $signatureKey = $request->header('X-Signature-Key') ?? $payload['signature_key'] ?? null;

        // 1. Validasi kehadiran signature
        if (!$signatureKey) {
            Log::warning('Midtrans webhook received without signature key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Missing signature'], 400);
        }

        // 2. Ambil server key untuk verifikasi
        $serverKey = config('services.midtrans.server_key');
        if (!$serverKey) {
            Log::error('Midtrans server_key not configured');
            return response()->json(['status' => 'error', 'message' => 'Server misconfiguration'], 500);
        }

        // 3. Verifikasi signature menggunakan SHA512
        $orderId = $payload['order_id'] ?? $payload['invoice_number'] ?? null;
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';

        $data = $orderId . $statusCode . $grossAmount;
        $expectedSignature = hash_hmac('sha512', $data, $serverKey);

        if (!hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('Midtrans webhook signature verification FAILED', [
                'ip' => $request->ip(),
                'order_id' => $orderId,
                'received_signature' => substr($signatureKey, 0, 20) . '...',
                'expected_signature' => substr($expectedSignature, 0, 20) . '...',
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        // 4. Log webhook diterima (berhasil verify)
        Log::info('Midtrans webhook verified successfully', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
            'transaction_id' => $payload['transaction_id'] ?? null,
        ]);

        // 5. Ekstrak field dari payload
        $invoice = $payload['order_id'] ?? $payload['invoice_number'] ?? null;
        $status = $payload['transaction_status'] ?? $payload['status'] ?? null;
        $reference = $payload['transaction_id'] ?? $payload['reference'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'qris';

        if (!$invoice) {
            return response()->json(['status' => 'error', 'message' => 'Missing order ID'], 400);
        }

        // 6. Cari order (dengan lock untuk mencegah race condition)
        $order = Order::where('invoice_number', $invoice)->lockForUpdate()->first();

        if (!$order) {
            Log::warning('Midtrans webhook: order not found', ['invoice' => $invoice]);
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // 7. Proses berdasarkan status pembayaran
        if (in_array($status, ['settlement', 'capture', 'success', 'PAID'])) {
            // Hanya proses jika order belum dibayar
            if ($order->status === 'pending' || $order->status === 'menunggu_pembayaran') {
                DB::transaction(function () use ($order, $reference, $paymentType, $payload) {
                    $order->update([
                        'status' => 'paid',
                        'payment_method' => $paymentType,
                        'payment_reference' => $reference ?: ('TX-' . strtoupper(uniqid())),
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $payload['transaction_id'] ?? $order->midtrans_transaction_id,
                    ]);

                    Log::info('Order marked as PAID via webhook', [
                        'order_id' => $order->id,
                        'invoice' => $order->invoice_number,
                        'payment_method' => $paymentType,
                        'reference' => $reference,
                        'midtrans_trans_id' => $payload['transaction_id'] ?? null,
                    ]);
                });
            } else {
                Log::info('Midtrans webhook: order already processed', [
                    'order_id' => $order->id,
                    'current_status' => $order->status,
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Payment verified successfully']);
        }

        // 8. Handle status lain (pending, expire, cancel, deny, failure)
        if (in_array($status, ['pending'])) {
            Log::info('Midtrans webhook: payment pending (waiting for customer)', [
                'order_id' => $order->id,
            ]);
        } elseif (in_array($status, ['expire', 'cancel', 'deny', 'failure'])) {
            // Update status order kalau perlu, tapi jangan langsung batalkan
            // Biar buyer bisa resett mengulang pembayaran
            if ($order->status === 'pending' || $order->status === 'menunggu_pembayaran') {
                Log::info('Midtrans webhook: payment failed/expired', [
                    'order_id' => $order->id,
                    'status' => $status,
                ]);
            }
        }

        return response()->json(['status' => 'ignored', 'message' => 'Status not actionable']);
    }

    /**
     * Simulator Pembayaran Instan (Sandbox / Demo Testing di UI).
     */
    public function simulateInstantPayment(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (app()->isProduction() && !in_array(Auth::user()?->role, ['admin', 'super_admin'])) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Simulasi pembayaran hanya diizinkan pada mode pengujian lokal.'], 403);
            }
            return back()->with('error', 'Simulasi pembayaran dinonaktifkan pada lingkungan produksi.');
        }

        $method = $request->input('method', 'qris');
        PaymentService::handlePaymentSuccess($order, 'SIM-' . time(), $method);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Pembayaran instan berhasil diverifikasi secara otomatis!',
            ]);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', "Pembayaran pesanan #{$order->invoice_number} berhasil diverifikasi otomatis!");
    }
}
