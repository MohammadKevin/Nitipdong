<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'product_id',
    'quantity',
])]
class Cart extends Model
{
    use HasFactory;

    /**
     * Relasi ke User / Customer pemilik item keranjang
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Produk yang dimasukkan ke keranjang
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}