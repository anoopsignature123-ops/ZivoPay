<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'shop_payments';

    protected $fillable = [
        'order_id', 'user_id', 'provider', 'mode', 'currency', 'amount', 'status', 'payment_order_id',
        'payment_id', 'refund_id', 'method', 'metadata', 'paid_at', 'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2', 'metadata' => 'array', 'paid_at' => 'datetime', 'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
