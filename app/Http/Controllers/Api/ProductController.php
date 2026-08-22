<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get paginated product list with search, category, and sorting filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'store'])->where('is_active', true);

        // Search Keyword
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->orWhere('id', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Store Filter
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sold_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = (int) $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $products->map(fn($p) => $this->formatProduct($p)),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
            ],
        ]);
    }

    /**
     * Get active Flash Sale items with live countdown metadata.
     */
    public function flashSale(): JsonResponse
    {
        $activeSale = FlashSale::with(['items.product.category', 'items.product.store'])
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();

        if (!$activeSale) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada promo Flash Sale aktif saat ini.',
                'data'    => [],
            ]);
        }

        $items = $activeSale->items->map(function ($item) {
            $p = $item->product;
            if (!$p) return null;

            $soldQty = $item->stock_sold ?? 0;
            $stockQty = $item->stock_allocated ?? 0;
            $progress = $item->sold_percentage ?? 0;

            return [
                'id'                  => $p->id,
                'name'                => $p->name,
                'original_price'      => (float) $p->price,
                'flash_sale_price'    => (float) $item->flash_sale_price,
                'discount_percentage' => (int) $item->discount_percentage,
                'image_url'           => $p->image_url ?? asset('img/saksershop-logo.png'),
                'sold_quantity'       => $soldQty,
                'stock_quantity'      => $stockQty,
                'sold_percentage'     => $progress,
                'store_name'          => $p->store->name ?? 'Official Store',
                'category_name'       => $p->category->name ?? 'Produk',
            ];
        })->filter()->values();

        return response()->json([
            'success'   => true,
            'title'     => $activeSale->title,
            'end_time'  => $activeSale->end_time->toIso8601String(),
            'remaining_seconds' => max(0, now()->diffInSeconds($activeSale->end_time, false)),
            'data'      => $items,
        ]);
    }

    /**
     * Get single product detail by ID or Slug.
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['category', 'store', 'discussions.replies.user', 'discussions.user'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        // Image List
        $images = array_filter(array_merge(
            $product->image ? [$product->image] : [],
            $product->images ?? []
        ));
        $formattedImages = array_map(function ($img) {
            if (str_starts_with($img, 'http')) return $img;
            if (str_starts_with($img, 'img/')) return asset($img);
            return asset('storage/' . $img);
        }, $images);

        if (empty($formattedImages)) {
            $formattedImages = [asset('img/saksershop-logo.png')];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $product->id,
                'name'                => $product->name,
                'slug'                => $product->slug,
                'price'               => (float) $product->price,
                'final_price'         => (float) $product->final_price,
                'has_discount'        => (bool) $product->has_discount,
                'discount_percentage' => (int) $product->discount_percentage_effective,
                'stock'               => (int) $product->stock,
                'weight'              => (int) ($product->weight ?? 500),
                'description'         => $product->description,
                'rating'              => (float) $product->effective_rating,
                'sold_count'          => (int) $product->sold_count,
                'images'              => $formattedImages,
                'variants'            => $product->variants ?? [],
                'category'            => [
                    'id'   => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ],
                'store'               => [
                    'id'          => $product->store->id,
                    'uuid'        => $product->store->uuid ?? null,
                    'name'        => $product->store->name,
                    'city'        => $product->store->effective_city ?? 'Jakarta',
                    'rating'      => (float) ($product->store->rating ?? 3.5),
                    'is_official' => true,
                    'logo_url'    => $product->store->logo_url,
                ],
                'discussions'         => $product->discussions->map(function ($disc) {
                    return [
                        'id'         => $disc->id,
                        'body'       => $disc->body,
                        'user_name'  => $disc->user?->name ?? 'Pembeli',
                        'is_seller'  => (bool) $disc->is_seller,
                        'created_at' => $disc->created_at->format('d M Y, H:i'),
                        'replies'    => $disc->replies->map(function ($reply) {
                            return [
                                'id'         => $reply->id,
                                'body'       => $reply->body,
                                'user_name'  => $reply->user?->name ?? 'Penjual',
                                'is_seller'  => (bool) $reply->is_seller,
                                'created_at' => $reply->created_at->format('d M Y, H:i'),
                            ];
                        }),
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Helper to format product data for JSON response.
     */
    private function formatProduct(Product $product): array
    {
        return [
            'id'                  => $product->id,
            'uuid'                => $product->uuid ?? null,
            'name'                => $product->name,
            'slug'                => $product->slug,
            'price'               => (float) $product->price,
            'final_price'         => (float) $product->final_price,
            'has_discount'        => (bool) $product->has_discount,
            'discount_percentage' => (int) $product->discount_percentage_effective,
            'rating'              => (float) $product->effective_rating,
            'sold_count'          => (int) $product->sold_count,
            'formatted_sold'      => (string) $product->formatted_sold,
            'stock'               => (int) $product->stock,
            'image_url'           => $product->image_url,
            'images'              => $product->images_urls,
            'category_name'       => $product->category->name ?? 'Produk',
            'store_name'          => $product->store->name ?? 'NitipDong',
            'store_id'            => $product->store_id,
            'store_uuid'          => $product->store->uuid ?? null,
            'store_rating'        => (float) ($product->store->rating ?? 3.5),
            'city'                => $product->store->effective_city ?? 'Jakarta',
            'badge'               => $product->badge,
            'is_featured'         => (bool) $product->is_featured,
        ];
    }

    /**
     * Post a new discussion question on a product.
     */
    public function storeDiscussion(Request $request, $id): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $product = Product::findOrFail($id);
        $user = $request->user();
        $isSeller = $user->store && $user->store->id === $product->store_id;

        $discussion = \App\Models\ProductDiscussion::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'parent_id'  => null,
            'body'       => $request->body,
            'is_seller'  => $isSeller,
        ]);

        if (!$isSeller && $product->store && $product->store->user_id) {
            \App\Models\AppNotification::create([
                'user_id' => $product->store->user_id,
                'title'   => 'Pertanyaan Baru di Produk Anda',
                'message' => "{$user->name} menanyakan: \"" . \Illuminate\Support\Str::limit($request->body, 60) . "\" pada produk {$product->name}",
                'link'    => route('product.show', $product) . '#discussion-' . $discussion->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan Anda berhasil dikirim ke diskusi produk!',
            'data'    => $discussion,
        ], 201);
    }

    /**
     * Reply to an existing discussion question.
     */
    public function replyDiscussion(Request $request, $id, $discussionId): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $product = Product::findOrFail($id);
        $discussion = \App\Models\ProductDiscussion::where('product_id', $product->id)->findOrFail($discussionId);

        $user = $request->user();
        $isSeller = $user->store && $user->store->id === $product->store_id;

        $parentId = $discussion->parent_id ?: $discussion->id;

        $reply = \App\Models\ProductDiscussion::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'parent_id'  => $parentId,
            'body'       => $replyBody = $request->body,
            'is_seller'  => $isSeller,
        ]);

        if ($discussion->user_id !== $user->id) {
            \App\Models\AppNotification::create([
                'user_id' => $discussion->user_id,
                'title'   => $isSeller ? 'Penjual Membalas Pertanyaan Anda' : 'Ada Balasan di Diskusi Produk',
                'message' => "{$user->name} membalas pertanyaan Anda pada produk {$product->name}",
                'link'    => route('product.show', $product) . '#discussion-' . $parentId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan Anda berhasil dikirim!',
            'data'    => $reply,
        ], 201);
    }
}
