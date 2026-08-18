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
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_percentage',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price'               => 'float',
        'discount_percentage' => 'integer',
        'stock'               => 'integer',
        'is_active'           => 'boolean',
    ];

    protected $appends = [
        'final_price',
        'has_discount',
        'discount_savings',
        'is_in_flash_sale',
    ];

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
            return round($this->price * (1 - ($this->discount_percentage / 100)));
        }

        return (float) $this->price;
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
        return max(0, $this->price - $this->final_price);
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
}