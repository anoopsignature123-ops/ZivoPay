<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanApplication extends Model
{
    use HasFactory;

    public const STATUSES = ['submitted', 'under_review', 'approved', 'rejected', 'disbursed', 'completed'];

    protected $fillable = ['application_number', 'user_id', 'loan_category_id', 'applicant_name', 'date_of_birth', 'mobile', 'email', 'address', 'city', 'state', 'pin_code', 'pan_number', 'aadhaar_number', 'employment_type', 'monthly_income', 'employer_name', 'existing_monthly_emi', 'credit_score', 'preferred_lender', 'loan_amount', 'tenure_years', 'purpose', 'specifics', 'declaration_accepted', 'status', 'admin_remarks', 'submitted_at', 'approved_at'];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'pan_number' => 'encrypted', 'aadhaar_number' => 'encrypted', 'monthly_income' => 'decimal:2', 'existing_monthly_emi' => 'decimal:2', 'loan_amount' => 'decimal:2', 'specifics' => 'array', 'declaration_accepted' => 'boolean', 'submitted_at' => 'datetime', 'approved_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LoanCategory::class, 'loan_category_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LoanApplicationDocument::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LoanApplicationStatusHistory::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(LoanApplicationFollowup::class);
    }
}
