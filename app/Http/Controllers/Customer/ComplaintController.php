<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\OrderComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if (in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Pesanan yang belum dibayar atau dibatalkan tidak dapat dikomplain.');
        }

        $existing = OrderComplaint::where('order_id', $order->id)->whereIn('status', ['pending', 'approved'])->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pengajuan komplain aktif untuk pesanan ini.');
        }

        $request->validate([
            'reason'      => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'photo'       => ['nullable', 'image', 'max:3072'],
        ], [
            'reason.required'      => 'Pilih alasan komplain pesanan.',
            'description.required' => 'Jelaskan detail kendala atau kerusakan produk.',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('complaints', 'public');
        }

        $complaint = OrderComplaint::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'store_id'    => $order->store_id,
            'reason'      => $request->reason,
            'description' => $request->description,
            'photo_url'   => $photoUrl,
            'status'      => 'pending',
        ]);

        // Notify seller
        if ($order->store && $order->store->user_id) {
            AppNotification::send(
                $order->store->user_id,
                'Komplain Pesanan Masuk',
                "Pembeli mengajukan komplain pada pesanan #{$order->invoice_number}: {$request->reason}",
                'complaint',
                route('seller.complaints.index')
            );
        }

        return back()->with('success', 'Komplain pesanan berhasil diajukan. Penjual akan meninjau dan merespon klaim Anda.');
    }
}
