<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'full_address',
        'city',
        'district',
        'village',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $appends = [
        'obfuscated_id',
        'formatted_address_line',
    ];

    public function getFormattedAddressLineAttribute(): string
    {
        $parts = array_filter([
            $this->full_address,
            $this->village ? 'Kel. ' . $this->village : null,
            $this->district ? 'Kec. ' . $this->district : null,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
