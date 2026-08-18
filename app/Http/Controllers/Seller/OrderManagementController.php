<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderManagementController extends Controller
{
    public function index(): View
    {
        $store = Auth::user()->store;

        $orders = Order::with(['user', 'orderItems.product'])
            ->where('store_id', $store?->id)
            ->latest()
            ->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->store_id !== Auth::user()->store?->id) {
            abort(403);
        }

        $request->validate([
            'status'          => ['required', 'in:pending,processing,shipped,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update([
            'status'          => $request->status,
            'tracking_number' => $request->tracking_number,
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}