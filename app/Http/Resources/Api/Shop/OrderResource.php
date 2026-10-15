<?php

namespace App\Http\Resources\Api\Shop;

use App\Services\Shop\ShopImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $images = app(ShopImageService::class);

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'subtotal' => (float) $this->subtotal,
            'delivery_fee' => (float) $this->delivery_fee,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'promo_code' => $this->promo_code,
            'tracking' => ['carrier' => $this->carrier, 'tracking_number' => $this->tracking_number],
            'delivery_address' => [
                'recipient_name' => $this->recipient_name,
                'phone' => $this->phone,
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'landmark' => $this->landmark,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ],
            'return_reason' => $this->return_reason,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'sku' => $item->product_sku,
                'image' => $images->url($item->product_image),
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'line_total' => (float) $item->line_total,
            ])->values()),
            'created_at' => $this->created_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
        ];
    }
}
