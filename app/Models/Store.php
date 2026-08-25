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
        'uuid',
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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $casts = [
        'balance' => 'float',
    ];

    protected $appends = [
        'obfuscated_id',
        'logo_url',
        'banner_url',
        'effective_city',
        'rating',
    ];

    public function getRatingAttribute(): float
    {
        $reviews = \App\Models\Review::whereHas('product', function ($q) {
            $q->where('store_id', $this->id);
        })->pluck('rating');

        // 1. Jika sudah ada ulasan rating dari pembeli
        if ($reviews->isNotEmpty()) {
            $avgReview = (float) $reviews->avg();
            return round($avgReview, 1);
        }

        // 2. Jika belum ada ulasan, cek pesanan sukses tanpa komplain
        $completedOrdersCount = $this->orders()->where('status', 'completed')->count();
        $complaintsCount = $this->complaints()->where('status', 'resolved_buyer_refund')->count();

        // Kenaikan reputasi bertahap jika ada pesanan selesai & 0 komplain
        if ($completedOrdersCount > 0 && $complaintsCount === 0) {
            $boost = min(1.5, $completedOrdersCount * 0.1);
            return round(min(5.0, 3.5 + $boost), 1);
        }

        // 3. Default rating awal toko baru = 3.5
        return 3.5;
    }

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
