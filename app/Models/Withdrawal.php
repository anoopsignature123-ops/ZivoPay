<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_wallet',
        'amount',
        'charge',
        'final_amount',
        'amount_inr',
        'usdt_amount',
        'conversion_rate',
        'provider_reference',
        'payment_method',
        'account_details',
        'trx_id',
        'status',
        'admin_remarks',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'amount_inr' => 'decimal:2',
        'usdt_amount' => 'decimal:6',
        'conversion_rate' => 'decimal:4',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
