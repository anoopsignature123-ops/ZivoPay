<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'amount',
        'daily_percentage',
        'daily_amount',
        'monthly_amount',
        'total_returned',
        'days_completed',
        'total_days',
        'status',
        'activated_at',
        'last_payout_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_percentage' => 'decimal:2',
        'daily_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'total_returned' => 'decimal:2',
        'activated_at' => 'datetime',
        'last_payout_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
