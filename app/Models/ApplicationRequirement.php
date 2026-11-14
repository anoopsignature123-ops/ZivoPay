<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['application_detail_id', 'coverage_amount', 'monthly_budget', 'coverage_duration', 'special_requirements'];

    protected function casts(): array
    {
        return ['coverage_amount' => 'decimal:2', 'monthly_budget' => 'decimal:2', 'coverage_duration' => 'integer'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }
}
