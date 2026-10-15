<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $table = 'shop_promo_codes';

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value', 'minimum_subtotal', 'maximum_discount',
        'usage_limit', 'usage_count', 'starts_at', 'ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2', 'minimum_subtotal' => 'decimal:2', 'maximum_discount' => 'decimal:2',
            'usage_limit' => 'integer', 'usage_count' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
