<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'service_type',
        'operator_code',
        'operator_name',
        'circle_code',
        'number',
        'value1',
        'value2',
        'amount',
        'status',
        'txid',
        'opid',
        'api_response',
        'admin_remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'api_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccess(): bool
    {
        return strtolower((string) $this->status) === 'success';
    }

    public function isFailed(): bool
    {
        return in_array(strtolower((string) $this->status), ['failed', 'failure', 'refunded'], true);
    }

    public function isPending(): bool
    {
        return strtolower((string) $this->status) === 'pending';
    }
}
