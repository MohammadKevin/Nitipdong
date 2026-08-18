<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'flash_sale_price',
        'discount_percentage',
        'stock_allocated',
        'stock_sold',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'flash_sale_price'    => 'decimal:2',
            'discount_percentage' => 'integer',
            'stock_allocated'     => 'integer',
            'stock_sold'          => 'integer',
            'is_active'           => 'boolean',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getSoldPercentageAttribute(): int
    {
        if ($this->stock_allocated <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->stock_sold / $this->stock_allocated) * 100));
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->stock_sold >= $this->stock_allocated;
    }

    public function getStockRemainingAttribute(): int
    {
        return max(0, $this->stock_allocated - $this->stock_sold);
    }
}
