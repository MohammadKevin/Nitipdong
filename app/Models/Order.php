<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, HasObfuscatedId;

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
            if (empty($order->invoice_number)) {
                $order->invoice_number = 'INV-' . strtoupper(Str::random(10));
            }
        });
    }

    protected $fillable = [
        'uuid',
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
        'snap_token',
        'tracking_number',
        'courier_id',
        'warehouse_id',
        'courier_lat',
        'courier_lng',
        'courier_location_updated_at',
        'delivery_proof_image',
        'delivery_notes',
    ];

    protected $casts = [
        'total_amount'    => 'float',
        'discount_amount' => 'float',
        'shipping_cost'   => 'float',
        'total_weight'    => 'float',
        'courier_lat'     => 'float',
        'courier_lng'     => 'float',
        'completed_at'    => 'datetime',
        'courier_location_updated_at' => 'datetime',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getOrderNumberAttribute(): string
    {
        return $this->invoice_number ?? ('ORD-' . $this->id);
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