<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'indicative_interest_rate', 'minimum_amount', 'maximum_amount', 'maximum_tenure_years', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['indicative_interest_rate' => 'decimal:2', 'minimum_amount' => 'decimal:2', 'maximum_amount' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }
}
