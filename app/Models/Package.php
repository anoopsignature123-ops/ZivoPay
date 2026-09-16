<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'daily_roi_percentage',
        'duration_days',
        'direct_bonus_percentage',
        'level_income_percentage',
        'status',
        'description',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'daily_roi_percentage' => 'decimal:2',
        'direct_bonus_percentage' => 'decimal:2',
        'level_income_percentage' => 'decimal:2',
        'duration_days' => 'integer',
    ];
}
