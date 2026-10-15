<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'shop_products';

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'price', 'mrp', 'stock',
        'image_paths', 'is_active', 'is_featured', 'is_flash_deal', 'flash_deal_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 'mrp' => 'decimal:2', 'stock' => 'integer', 'image_paths' => 'array',
            'is_active' => 'boolean', 'is_featured' => 'boolean', 'is_flash_deal' => 'boolean',
            'flash_deal_ends_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
