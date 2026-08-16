<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'invoice_number',
    'user_id',
    'store_id',
    'total_amount',
    'status',
    'payment_proof',
    'shipping_address',
    'tracking_number',
])]
class Order extends Model
{
    use HasFactory;

    /**
     * Relasi ke Customer pembuat pesanan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Toko / Seller tempat barang dibeli
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relasi ke rincian item barang dalam pesanan
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}