<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_detail_id', 'personal_name', 'occupation', 'annual_income', 'annual_income_range', 'nominee_name',
        'nominee_relation', 'policy_term', 'sum_assured', 'is_smoker',
    ];

    protected function casts(): array
    {
        return ['annual_income' => 'decimal:2', 'policy_term' => 'integer', 'sum_assured' => 'decimal:2'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }
}
