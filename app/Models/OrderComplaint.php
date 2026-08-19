<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderComplaint extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'order_id',
        'user_id',
        'store_id',
        'reason',
        'description',
        'photo_url',
        'status',
        'seller_response',
        'admin_notes',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
