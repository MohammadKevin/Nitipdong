<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'title',
        'start_time',
        'end_time',
        'is_active',
        'banner',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time'   => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class, 'flash_sale_id');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class, 'flash_sale_id')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('start_time', '<=', now())
                     ->where('end_time', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
                     ->where('start_time', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where('end_time', '<', now());
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->is_active && now()->between($this->start_time, $this->end_time);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->is_active && now()->lt($this->start_time);
    }

    public function getIsEndedAttribute(): bool
    {
        return now()->gt($this->end_time);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if ($this->is_running) {
            return (int) max(0, now()->diffInSeconds($this->end_time, false));
        }

        if ($this->is_upcoming) {
            return (int) max(0, now()->diffInSeconds($this->start_time, false));
        }

        return 0;
    }

    public function getStatusBadgeAttribute(): array
    {
        if (!$this->is_active) {
            return ['label' => 'Nonaktif', 'color' => 'bg-slate-100 text-slate-600 border-slate-200'];
        }

        if ($this->is_running) {
            return ['label' => 'Sedang Berlangsung', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'];
        }

        if ($this->is_upcoming) {
            return ['label' => 'Akan Datang', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'];
        }

        return ['label' => 'Telah Berakhir', 'color' => 'bg-amber-50 text-amber-700 border-amber-200'];
    }
}
