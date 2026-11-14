<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = ['application_detail_id', 'document_type', 'document_path', 'original_name', 'mime_type'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_detail_id');
    }
}
