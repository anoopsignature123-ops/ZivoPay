<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'shop_orders';

    protected $fillable = [
        'user_id', 'address_id', 'order_number', 'status', 'payment_method', 'payment_status', 'subtotal',
        'delivery_fee', 'discount', 'shopping_discount', 'total', 'promo_code', 'recipient_name', 'phone', 'address_line1',
        'address_line2', 'landmark', 'city', 'state', 'postal_code', 'country', 'tracking_number', 'carrier',
        'customer_note', 'admin_note', 'return_reason', 'return_items', 'refunded_amount', 'return_requested_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2', 'delivery_fee' => 'decimal:2', 'discount' => 'decimal:2',
            'total' => 'decimal:2', 'refunded_amount' => 'decimal:2', 'shopping_discount' => 'decimal:2', 'return_items' => 'array',
            'return_requested_at' => 'datetime', 'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
