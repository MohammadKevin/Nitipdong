<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(Order $order): View
    {
        $user = Auth::user();
        $isCustomer = $order->user_id === $user->id;
        $isSeller = $user->store && $order->store_id === $user->store->id;
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);

        if (!$isCustomer && !$isSeller && !$isAdmin) {
            abort(403, 'Akses tidak sah untuk melihat invoice ini.');
        }

        $order->load(['user', 'store', 'orderItems.product']);

        return view('invoice.show', compact('order'));
    }

    public function shippingLabel(Order $order): View
    {
        $user = Auth::user();
        $isSeller = $user->store && $order->store_id === $user->store->id;
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);

        if (!$isSeller && !$isAdmin) {
            abort(403, 'Akses khusus penjual untuk mencetak label pengiriman.');
        }

        $order->load(['user', 'store', 'orderItems.product']);

        return view('invoice.shipping_label', compact('order'));
    }
}
