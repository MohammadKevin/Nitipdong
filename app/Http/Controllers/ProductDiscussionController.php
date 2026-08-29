<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\ProductDiscussion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductDiscussionController extends Controller
{
    /**
     * Tanyakan pertanyaan baru di produk.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $user = Auth::user();
        $isSeller = $user->store && $user->store->id === $product->store_id;

        $discussion = ProductDiscussion::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'parent_id'  => null,
            'body'       => $request->body,
            'is_seller'  => $isSeller,
        ]);

        // Kirim notifikasi ke Penjual jika pembeli yang bertanya
        if (!$isSeller && $product->store && $product->store->user_id) {
            AppNotification::send(
                $product->store->user_id,
                'Pertanyaan Baru di Produk Anda',
                "{$user->name} menanyakan: \"" . \Illuminate\Support\Str::limit($request->body, 60) . "\" pada produk {$product->name}",
                'discussion',
                route('product.show', $product) . '#discussion-' . $discussion->id
            );
        }

        return back()->with('success', 'Pertanyaan Anda berhasil dikirim ke diskusi produk!');
    }

    /**
     * Balas pertanyaan di diskusi produk.
     */
    public function reply(Request $request, Product $product, ProductDiscussion $discussion): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $user = Auth::user();
        $isSeller = $user->store && $user->store->id === $product->store_id;

        // Pastikan balasan menempel pada parent teratas jika membalas child
        $parentId = $discussion->parent_id ?: $discussion->id;

        $reply = ProductDiscussion::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'parent_id'  => $parentId,
            'body'       => $request->body,
            'is_seller'  => $isSeller,
        ]);

        // Notifikasi ke penanya asli
        if ($discussion->user_id !== $user->id) {
            AppNotification::send(
                $discussion->user_id,
                $isSeller ? 'Penjual Membalas Pertanyaan Anda' : 'Ada Balasan di Diskusi Produk',
                "{$user->name} membalas pertanyaan Anda pada produk {$product->name}",
                'discussion',
                route('product.show', $product) . '#discussion-' . $parentId
            );
        }

        return back()->with('success', 'Balasan Anda berhasil dikirim!');
    }

    /**
     * Hapus pertanyaan / balasan.
     */
    public function destroy(ProductDiscussion $discussion): RedirectResponse
    {
        $user = Auth::user();
        $isAuthor = $discussion->user_id === $user->id;
        $isStoreOwner = $user->store && $discussion->product && $discussion->product->store_id === $user->store->id;
        $isAdmin = in_array($user->role, ['admin', 'super_admin']);

        if (!$isAuthor && !$isStoreOwner && !$isAdmin) {
            abort(403, 'Akses tidak sah untuk menghapus diskusi ini.');
        }

        $discussion->delete();

        return back()->with('success', 'Diskusi berhasil dihapus.');
    }
}
