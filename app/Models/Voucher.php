<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'code',
        'name',
        'description',
        'type',
        'amount',
        'min_spend',
        'max_discount',
        'quota',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'store_id'     => 'integer',
        'amount'       => 'float',
        'min_spend'    => 'float',
        'max_discount' => 'float',
        'quota'        => 'integer',
        'is_active'    => 'boolean',
        'expires_at'   => 'datetime',
    ];

    protected $appends = [
        'is_platform_voucher',
        'is_store_voucher',
    ];

    public function getIsPlatformVoucherAttribute(): bool
    {
        return is_null($this->store_id);
    }

    public function getIsStoreVoucherAttribute(): bool
    {
        return !is_null($this->store_id);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where('quota', '>', 0);
    }

    public function scopePlatform(Builder $query): Builder
    {
        return $query->whereNull('store_id');
    }

    public function scopeForStore(Builder $query, int $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

    public function validateForSubtotal(float $subtotal): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Voucher ini sudah tidak aktif.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Voucher ini sudah kadaluarsa.'];
        }

        if ($this->quota <= 0) {
            return ['valid' => false, 'message' => 'Kuota pemakaian voucher ini sudah habis.'];
        }

        if ($subtotal < $this->min_spend) {
            return [
                'valid'   => false,
                'message' => 'Minimal belanja untuk voucher ini adalah Rp' . number_format($this->min_spend, 0, ',', '.'),
            ];
        }

        return ['valid' => true, 'message' => 'Voucher berhasil diterapkan!'];
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_spend) {
            return 0.0;
        }

        if ($this->type === 'fixed') {
            return min($this->amount, $subtotal);
        }

        $discount = ($this->amount / 100) * $subtotal;

        if ($this->max_discount && $this->max_discount > 0) {
            $discount = min($discount, $this->max_discount);
        }

        return round(min($discount, $subtotal));
    }
}
