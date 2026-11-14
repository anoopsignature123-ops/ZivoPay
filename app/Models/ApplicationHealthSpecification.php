<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationHealthSpecification extends Model
{
    use HasFactory;

    protected $fillable = ['application_detail_id', 'personal_name', 'plan_type', 'number_of_dependents', 'pre_existing_conditions', 'current_medications'];

    protected function casts(): array
    {
        return ['number_of_dependents' => 'integer'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }
}
