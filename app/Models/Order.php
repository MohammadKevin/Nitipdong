<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'store_id',
        'total_amount',
        'voucher_code',
        'discount_amount',
        'status',
        'completed_at',
        'payment_proof',
        'shipping_address',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'total_weight',
        'payment_method',
        'payment_reference',
        'tracking_number',
    ];

    protected $casts = [
        'total_amount'    => 'float',
        'discount_amount' => 'float',
        'shipping_cost'   => 'float',
        'total_weight'    => 'float',
        'completed_at'    => 'datetime',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function complaint(): HasOne
    {
        return $this->hasOne(OrderComplaint::class)->latestOfMany();
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(OrderComplaint::class);
    }
}