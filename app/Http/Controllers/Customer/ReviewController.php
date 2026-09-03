<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan setelah pesanan selesai diterima.');
        }

        $request->validate([
            'order_item_id' => ['required', 'exists:order_items,id'],
            'rating'        => ['required', 'integer', 'between:1,5'],
            'comment'       => ['nullable', 'string', 'max:2000'],
            'images'        => ['nullable', 'array', 'max:5'],
            'images.*'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_anonymous'  => ['nullable', 'boolean'],
        ]);

        $orderItem = OrderItem::where('id', $request->order_item_id)
            ->where('order_id', $order->id)
            ->firstOrFail();

        if (Review::where('order_item_id', $orderItem->id)->exists()) {
            return back()->with('error', 'Produk ini sudah Anda beri ulasan sebelumnya.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $orderItem->product_id,
            'order_id'      => $order->id,
            'order_item_id' => $orderItem->id,
            'rating'        => (int) $request->rating,
            'comment'       => $request->comment,
            'images'        => !empty($imagePaths) ? $imagePaths : null,
            'is_anonymous'  => $request->boolean('is_anonymous'),
        ]);

        // Update rating agregat produk
        $orderItem->product?->recalculateRating();

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }
}
