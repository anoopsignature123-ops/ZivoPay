<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deposit_ref',
        'amount',
        'charge',
        'final_amount',
        'payment_method',
        'trx_hash',
        'proof_file',
        'gateway_reference',
        'status',
        'admin_remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
