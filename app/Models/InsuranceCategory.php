<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image', 'display_order', 'is_active'];

    protected function casts(): array
    {
        return ['display_order' => 'integer', 'is_active' => 'boolean'];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(InsuranceCompany::class, 'insurance_category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(InsuranceProduct::class, 'insurance_category_id');
    }

    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class, 'insurance_category_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'insurance_category_id');
    }
}
