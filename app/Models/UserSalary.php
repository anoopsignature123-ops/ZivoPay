<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rank_level',
        'rank_name',
        'matching_requirement',
        'business_requirement',
        'monthly_reward',
        'total_months',
        'months_paid',
        'total_paid',
        'status',
        'last_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'rank_level' => 'integer',
            'matching_requirement' => 'decimal:2',
            'business_requirement' => 'decimal:2',
            'monthly_reward' => 'decimal:2',
            'total_months' => 'integer',
            'months_paid' => 'integer',
            'total_paid' => 'decimal:2',
            'last_paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
