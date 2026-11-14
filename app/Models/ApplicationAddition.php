<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAddition extends Model
{
    use HasFactory;

    protected $fillable = ['application_detail_id', 'is_existing_policy', 'contact_time', 'language_preference'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }
}
