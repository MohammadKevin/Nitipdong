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

    public const STATUS_PENDING    = 'pending';
    public const STATUS_PAID       = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    /**
     * Allowed State Transitions for Finite State Machine (FSM).
     */
    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => [
            self::STATUS_PAID,
            self::STATUS_PROCESSING,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_PAID => [
            self::STATUS_PROCESSING,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_PROCESSING => [
            self::STATUS_SHIPPED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_SHIPPED => [
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_DELIVERED => [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_COMPLETED => [], // Terminal state
        self::STATUS_CANCELLED => [], // Terminal state
    ];

    /**
     * Check whether an order can transition from current status to a target status.
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        if ($this->status === $targetStatus) {
            return true;
        }

        $allowed = self::ALLOWED_TRANSITIONS[$this->status] ?? [];
        return in_array($targetStatus, $allowed, true);
    }

    /**
     * Check if order is in a terminal state (completed or cancelled).
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }

    /**
     * Transition order status with strict FSM validation.
     */
    public function transitionTo(string $targetStatus, array $extraAttributes = []): bool
    {
        if ($this->status === $targetStatus) {
            if (!empty($extraAttributes)) {
                return $this->update($extraAttributes);
            }
            return true;
        }

        if ($this->isTerminal()) {
            throw new \DomainException("Pesanan #{$this->invoice_number} sudah berada pada status final '{$this->status}' dan tidak dapat diubah lagi.");
        }

        if (!$this->canTransitionTo($targetStatus)) {
            throw new \DomainException("Transisi status pesanan dari '{$this->status}' menuju '{$targetStatus}' tidak valid.");
        }

        $attributes = array_merge(['status' => $targetStatus], $extraAttributes);

        if ($targetStatus === self::STATUS_COMPLETED && empty($this->completed_at)) {
            $attributes['completed_at'] = now();
        }

        return $this->update($attributes);
    }

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
        'expires_at',
        'seller_credited_at',
        'shipping_status',
    ];

    protected $casts = [
        'total_amount'    => 'float',
        'discount_amount' => 'float',
        'shipping_cost'   => 'float',
        'total_weight'    => 'float',
        'courier_lat'     => 'float',
        'courier_lng'     => 'float',
        'completed_at'    => 'datetime',
        'expires_at'      => 'datetime',
        'seller_credited_at' => 'datetime',
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
