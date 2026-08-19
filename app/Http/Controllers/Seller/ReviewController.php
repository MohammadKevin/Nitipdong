<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $store = Auth::user()->store;

        $reviews = Review::with(['user', 'product', 'order'])
            ->whereHas('product', function ($q) use ($store) {
                $q->where('store_id', $store?->id);
            })
            ->latest()
            ->paginate(15);

        return view('seller.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $store = Auth::user()->store;

        if ($review->product->store_id !== $store?->id) {
            abort(403, 'Akses tidak sah.');
        }

        $request->validate([
            'seller_reply' => ['required', 'string', 'max:2000'],
        ]);

        $review->update([
            'seller_reply'      => $request->seller_reply,
            'seller_replied_at' => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }
}
