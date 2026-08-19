<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\OrderComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(): View
    {
        $store = Auth::user()->store;
        if (!$store) {
            abort(403, 'Akses tidak sah.');
        }

        $complaints = OrderComplaint::where('store_id', $store->id)
            ->with(['order.orderItems.product', 'user'])
            ->latest()
            ->paginate(10);

        return view('seller.complaints.index', compact('complaints', 'store'));
    }

    public function respond(Request $request, OrderComplaint $complaint): RedirectResponse
    {
        $store = Auth::user()->store;
        if (!$store || $complaint->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($complaint->status !== 'pending') {
            return back()->with('error', 'Komplain ini sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'decision'        => ['required', 'in:approve,reject'],
            'seller_response' => ['required', 'string', 'max:500'],
        ], [
            'seller_response.required' => 'Wajib memberikan respon/tanggapan tertulis kepada pembeli.',
        ]);

        DB::transaction(function () use ($request, $complaint) {
            $isApproved = $request->decision === 'approve';
            $complaint->update([
                'status'          => $isApproved ? 'approved' : 'rejected',
                'seller_response' => $request->seller_response,
            ]);

            $order = $complaint->order;

            if ($isApproved) {
                // If the order was completed, adjust seller balance
                if ($order->status === 'completed') {
                    $refundedAmount = $order->total_amount * 0.95;
                    $complaint->store->decrement('balance', min($complaint->store->balance, $refundedAmount));
                }
                $order->update(['status' => 'cancelled']);
            }

            // Notify buyer
            AppNotification::send(
                $complaint->user_id,
                $isApproved ? 'Komplain Pesanan Disetujui' : 'Tanggapan Komplain Pesanan',
                $isApproved 
                    ? "Penjual telah menyetujui komplain untuk pesanan #{$order->invoice_number}. Dana belanja Anda diproses kembali."
                    : "Penjual memberikan tanggapan atas komplain pesanan #{$order->invoice_number}: {$request->seller_response}",
                'complaint',
                route('customer.dashboard')
            );
        });

        return back()->with('success', 'Tanggapan komplain berhasil dikirim.');
    }
}
