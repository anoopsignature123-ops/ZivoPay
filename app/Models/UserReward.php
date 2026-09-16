<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_title',
        'business_required',
        'reward_item',
        'type',
        'status',
        'achieved_at',
        'claimed_at',
    ];

    protected $casts = [
        'business_required' => 'decimal:2',
        'achieved_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
