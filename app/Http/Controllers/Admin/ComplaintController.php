<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\OrderComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Display dispute resolution center list for operational admin.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');

        $query = OrderComplaint::with(['order.orderItems.product', 'user', 'store.user'])
            ->latest();

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($oq) use ($search) {
                    $oq->where('invoice_number', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('store', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(15)->withQueryString();

        // Summary KPI Metrics
        $totalCount    = OrderComplaint::count();
        $pendingCount  = OrderComplaint::where('status', 'pending')->count();
        $approvedCount = OrderComplaint::where('status', 'approved')->count();
        $rejectedCount = OrderComplaint::where('status', 'rejected')->count();

        return view('admin.complaints.index', compact(
            'complaints',
            'status',
            'search',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Admin mediator decision on dispute.
     */
    public function resolve(Request $request, OrderComplaint $complaint): RedirectResponse
    {
        $request->validate([
            'decision'    => ['required', 'in:approve,reject'],
            'admin_notes' => ['required', 'string', 'max:1000'],
        ], [
            'admin_notes.required' => 'Wajib memberikan catatan resmi keputusan mediasi operasional.',
        ]);

        DB::transaction(function () use ($request, $complaint) {
            $isApproved = $request->decision === 'approve';
            $complaint->update([
                'status'      => $isApproved ? 'approved' : 'rejected',
                'admin_notes' => $request->admin_notes,
            ]);

            $order = $complaint->order;

            if ($isApproved && $order) {
                if ($order->status === 'completed' && $complaint->store) {
                    $refundedAmount = $order->total_amount * 0.85;
                    $complaint->store->decrement('balance', min($complaint->store->balance, $refundedAmount));
                }

                // Restore stock and decrement sold count
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                        if ($item->product->sold_count > 0) {
                            $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                        }
                    }
                }

                // Restore voucher quota if used
                if (!empty($order->voucher_code)) {
                    $voucher = \App\Models\Voucher::where('code', $order->voucher_code)->first();
                    if ($voucher && $voucher->quota !== null) {
                        $voucher->increment('quota');
                    }
                }

                $order->update(['status' => 'cancelled']);
            }

            // 1. Notify Buyer
            AppNotification::send(
                $complaint->user_id,
                $isApproved ? 'Mediasi Komplain Disetujui (Admin)' : 'Mediasi Komplain Ditolak (Admin)',
                $isApproved
                    ? "Admin Operasional telah menyetujui komplain pesanan #{$order->invoice_number}. Catatan: {$request->admin_notes}"
                    : "Admin Operasional telah menolak komplain pesanan #{$order->invoice_number}. Catatan: {$request->admin_notes}",
                'complaint',
                route('customer.dashboard')
            );

            // 2. Notify Seller if store user exists
            if ($complaint->store && $complaint->store->user_id) {
                AppNotification::send(
                    $complaint->store->user_id,
                    'Keputusan Mediasi Komplain Platform',
                    "Admin Operasional memutuskan sengketa pesanan #{$order->invoice_number}: " . ($isApproved ? 'Disetujui Pengembalian' : 'Ditolak') . ". Catatan: {$request->admin_notes}",
                    'complaint',
                    route('seller.complaints.index')
                );
            }
        });

        return back()->with('success', 'Keputusan mediasi sengketa berhasil disimpan dan notifikasi telah dikirim.');
    }
}
