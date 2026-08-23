<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'province',
        'address',
        'lat',
        'lng',
        'phone',
        'pic_name',
        'is_active',
    ];

    protected $casts = [
        'lat'       => 'float',
        'lng'       => 'float',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Cari Gudang Hub DC terdekat berdasarkan nama kota atau provinsi
     */
    public static function findNearestForCity(?string $cityName): ?self
    {
        if (empty($cityName)) {
            return self::where('is_active', true)->first();
        }

        $cleanCity = strtolower(trim($cityName));

        // Exact / Like search in City
        $match = self::where('is_active', true)
            ->where(function ($q) use ($cleanCity) {
                $q->whereRaw('LOWER(city) LIKE ?', ["%{$cleanCity}%"])
                  ->orWhereRaw('LOWER(name) LIKE ?', ["%{$cleanCity}%"])
                  ->orWhereRaw('LOWER(province) LIKE ?', ["%{$cleanCity}%"]);
            })
            ->first();

        return $match ?: self::where('is_active', true)->first();
    }
}
