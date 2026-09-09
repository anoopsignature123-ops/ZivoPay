<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMatchingRoiContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matching_amount',
        'daily_amount',
        'duration_days',
        'days_paid',
        'total_paid',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'matching_amount' => 'decimal:2',
            'daily_amount' => 'decimal:2',
            'duration_days' => 'integer',
            'days_paid' => 'integer',
            'total_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
