<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = ['insurance_company_id', 'insurance_category_id', 'insurance_product_id', 'name', 'slug', 'description', 'image', 'display_order', 'is_active'];

    protected function casts(): array
    {
        return ['display_order' => 'integer'];
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): bool => $value === 'yes',
            set: fn (bool|string|null $value): string => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'yes' : 'no',
        );
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

    public function generalSpecifications(): HasMany
    {
        return $this->hasMany(ApplicationGeneralSpecification::class, 'product_type_id');
    }
}
