<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = ['name', 'slug', 'icon'];

    protected $appends = [
        'obfuscated_id',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
