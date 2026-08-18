<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'category_id', 'name', 'slug', 'description', 'price',
        'discount_percentage', 'rating', 'sold_count', 'stock', 'image',
        'images', 'is_active', 'is_featured', 'badge'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    /**
     * Get all product images (main + additional)
     */
    public function getAllImages(): array
    {
        $allImages = [];

        // Tambahkan main image
        if ($this->image) {
            $allImages[] = $this->image;
        }

        // Tambahkan additional images
        if ($this->images && is_array($this->images)) {
            $allImages = array_merge($allImages, $this->images);
        }

        return $allImages;
    }

    /**
     * Get discounted price
     */
    public function getDiscountedPrice(): float
    {
        if ($this->discount_percentage > 0) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }
        return $this->price;
    }

    /**
     * Get original price (before discount)
     */
    public function getOriginalPrice(): float
    {
        return $this->price;
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
}
