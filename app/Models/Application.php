<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'submitted', 'under_review', 'approved', 'rejected'];

    protected $table = 'application_details';

    protected $fillable = [
        'application_number', 'user_id', 'insurance_company_id', 'insurance_product_id', 'insurance_category_id',
        'name', 'guardian_name', 'email', 'phone', 'date_of_birth', 'gender', 'address', 'district', 'state', 'pin_code',
        'status', 'is_declaration_accepted', 'admin_remarks', 'submitted_at', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_declaration_accepted' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InsuranceCategory::class, 'insurance_category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class, 'insurance_product_id');
    }

    public function specifications(): HasOne
    {
        return $this->hasOne(ApplicationSpecification::class, 'application_detail_id');
    }

    public function healthSpecifications(): HasOne
    {
        return $this->hasOne(ApplicationHealthSpecification::class, 'application_detail_id');
    }

    public function generalSpecifications(): HasOne
    {
        return $this->hasOne(ApplicationGeneralSpecification::class, 'application_detail_id');
    }

    public function requirements(): HasOne
    {
        return $this->hasOne(ApplicationRequirement::class, 'application_detail_id');
    }

    public function additions(): HasOne
    {
        return $this->hasOne(ApplicationAddition::class, 'application_detail_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'application_detail_id');
    }
}
