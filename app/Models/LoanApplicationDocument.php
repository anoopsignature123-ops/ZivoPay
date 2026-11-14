<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanApplicationDocument extends Model
{
    protected $fillable = ['loan_application_id', 'document_type', 'file_path', 'original_name', 'mime_type', 'verification_status', 'remarks'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
