<?php

namespace App\Models;

use App\Traits\HasObfuscatedId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory, HasObfuscatedId;

    protected $fillable = [
        'store_id',
        'amount',
        'bank_name',
        'account_number',
        'account_holder',
        'status',
        'admin_note',
        'proof_url',
        'approved_at',
    ];

    protected $casts = [
        'amount'      => 'float',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'obfuscated_id',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
