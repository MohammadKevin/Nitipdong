<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductDiscussion extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'body',
        'is_seller',
    ];

    protected $casts = [
        'is_seller' => 'boolean',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductDiscussion::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ProductDiscussion::class, 'parent_id')->oldest();
    }
}
