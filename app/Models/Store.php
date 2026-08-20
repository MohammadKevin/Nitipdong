<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'address',
        'city',
        'province',
        'district',
        'postal_code',
        'latitude',
        'longitude',
        'status',
        'balance',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
    ];

    protected $casts = [
        'balance' => 'float',
    ];

    protected $appends = [
        'obfuscated_id',
        'logo_url',
        'banner_url',
        'effective_city',
    ];

    public function getEffectiveCityAttribute(): string
    {
        if (!empty($this->city)) {
            return $this->city;
        }

        // Coba deteksi nama kota dari kolom address
        if (!empty($this->address)) {
            $addr = $this->address;
            $knownCities = [
                'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
                'Bandung', 'Kota Bandung', 'Bekasi', 'Kota Bekasi', 'Depok', 'Kota Depok', 'Bogor', 'Kota Bogor',
                'Surabaya', 'Kota Surabaya', 'Semarang', 'Kota Semarang', 'Yogyakarta', 'Kota Yogyakarta',
                'Malang', 'Kota Malang', 'Surakarta', 'Solo', 'Medan', 'Kota Medan', 'Makassar', 'Kota Makassar',
                'Denpasar', 'Kota Denpasar', 'Palembang', 'Kota Palembang', 'Tangerang', 'Kota Tangerang',
                'Tangerang Selatan', 'Batam', 'Kota Batam', 'Pekanbaru', 'Kota Pekanbaru'
            ];
            foreach ($knownCities as $kCity) {
                if (stripos($addr, $kCity) !== false) {
                    return $kCity;
                }
            }
        }

        return 'Jakarta Pusat';
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                return $this->logo;
            }
            return asset('storage/' . $this->logo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0891b2&color=fff&size=200&bold=true';
    }

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner) {
            if (str_starts_with($this->banner, 'http://') || str_starts_with($this->banner, 'https://')) {
                return $this->banner;
            }
            return asset('storage/' . $this->banner);
        }
        return null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(OrderComplaint::class);
    }
}