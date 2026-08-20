<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_percentage',
        'stock',
        'image',
        'images',
        'is_featured',
        'badge',
        'rating',
        'sold_count',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'integer',
        'stock'               => 'integer',
        'is_active'           => 'boolean',
        'is_featured'         => 'boolean',
        'images'              => 'array',
        'specifications'      => 'array',
        'variants'            => 'array',
        'rating'              => 'float',
        'sold_count'          => 'integer',
        'weight'              => 'float',
    ];

    protected $appends = [
        'seller_price',
        'platform_fee',
        'customer_base_price',
        'final_price',
        'has_discount',
        'discount_savings',
        'is_in_flash_sale',
        'obfuscated_id',
        'image_url',
        'images_urls',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        // Direct Cloudinary or external URL
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // Local public folder
        if (str_starts_with($this->image, 'img/')) {
            return asset($this->image);
        }

        // Local storage folder
        return asset('storage/' . $this->image);
    }

    public function getImagesUrlsAttribute(): array
    {
        if (! $this->images || ! is_array($this->images)) {
            return [];
        }

        return array_map(function ($img) {
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }
            if (str_starts_with($img, 'img/')) {
                return asset($img);
            }
            return asset('storage/' . $img);
        }, $this->images);
    }

    public function getSellerPriceAttribute(): float
    {
        return (float) ($this->attributes['price'] ?? 0);
    }

    public function getPlatformFeeAttribute(): float
    {
        return round($this->seller_price * 0.05);
    }

    public function getCustomerBasePriceAttribute(): float
    {
        return round($this->seller_price * 1.05);
    }

    public function getPriceAttribute(): float
    {
        return (float) $this->customer_base_price;
    }

    public function getCurrentFlashSaleItemAttribute(): ?FlashSaleItem
    {
        return $this->flashSaleItems()
            ->where('is_active', true)
            ->whereHas('flashSale', function ($q) {
                $q->where('is_active', true)
                  ->where('start_time', '<=', now())
                  ->where('end_time', '>=', now());
            })
            ->first();
    }

    public function getIsInFlashSaleAttribute(): bool
    {
        return $this->current_flash_sale_item !== null;
    }

    public function getFinalPriceAttribute(): float
    {
        if ($fsi = $this->current_flash_sale_item) {
            return (float) $fsi->flash_sale_price;
        }

        if ($this->discount_percentage > 0) {
            return round($this->customer_base_price * (1 - ($this->discount_percentage / 100)));
        }

        return (float) $this->customer_base_price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->is_in_flash_sale || $this->discount_percentage > 0;
    }

    public function getDiscountPercentageEffectiveAttribute(): int
    {
        if ($fsi = $this->current_flash_sale_item) {
            return $fsi->discount_percentage;
        }

        return $this->discount_percentage;
    }

    public function getDiscountSavingsAttribute(): float
    {
        return max(0, $this->customer_base_price - $this->final_price);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function flashSaleItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(ProductDiscussion::class)->whereNull('parent_id')->latest();
    }

    public function getSoldCountAttribute(): int
    {
        return (int) $this->orderItems()
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'completed']);
            })
            ->sum('quantity');
    }

    public function getFormattedSoldCountAttribute(): string
    {
        $count = $this->sold_count;
        if ($count >= 1000) {
            return number_format($count / 1000, 1, ',', '.') . ' rb+';
        }
        return (string) $count;
    }

    public function getEffectiveRatingAttribute(): float
    {
        $hasReviews = $this->relationLoaded('reviews')
            ? $this->reviews->isNotEmpty()
            : $this->reviews()->exists();

        if ($hasReviews) {
            $avg = $this->relationLoaded('reviews')
                ? $this->reviews->avg('rating')
                : $this->reviews()->avg('rating');

            if ($avg && (float)$avg > 0) {
                return round((float) $avg, 1);
            }
        }

        $base = (float) ($this->attributes['rating'] ?? 0);
        if ($base > 0) {
            return round($base, 1);
        }

        return 5.0; // Default standard 5.0 star rating for verified store catalog
    }

    public function getRatingAttribute(): float
    {
        return $this->getEffectiveRatingAttribute();
    }

    public function recalculateRating(): void
    {
        $avg = $this->reviews()->avg('rating') ?: 5.0;
        $this->update(['rating' => round($avg, 2)]);
    }

    public function isWishlistedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->wishlists()->where('user_id', $user->id)->exists();
    }
}
