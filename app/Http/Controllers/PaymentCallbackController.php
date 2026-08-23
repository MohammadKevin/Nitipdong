<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCallbackController extends Controller
{
    /**
     * Webhook Notification Handler (Midtrans / Payment Gateway).
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        $invoice = $payload['order_id'] ?? $payload['invoice_number'] ?? null;
        $status = $payload['transaction_status'] ?? $payload['status'] ?? null;
        $reference = $payload['transaction_id'] ?? $payload['reference'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'qris';

        if (! $invoice) {
            return response()->json(['status' => 'error', 'message' => 'Missing order ID'], 400);
        }

        $order = Order::where('invoice_number', $invoice)->first();

        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        if (in_array($status, ['settlement', 'capture', 'success', 'PAID'])) {
            PaymentService::handlePaymentSuccess($order, $reference, $paymentType);
            return response()->json(['status' => 'success', 'message' => 'Payment verified successfully']);
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
