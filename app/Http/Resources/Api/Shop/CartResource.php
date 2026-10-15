<?php

namespace App\Http\Resources\Api\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $items = $this->items->map(function ($item) use ($request): array {
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'line_total' => round((float) $item->product->price * $item->quantity, 2),
                'product' => ProductResource::make($item->product)->toArray($request),
            ];
        });
        $subtotal = round($items->sum('line_total'), 2);

        return [
            'id' => $this->id,
            'items' => $items->values(),
            'item_count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'delivery_fee' => $subtotal >= (float) config('shop.free_delivery_threshold') ? 0.0 : (float) config('shop.delivery_fee'),
            'free_delivery_threshold' => (float) config('shop.free_delivery_threshold'),
        ];
    }
}
