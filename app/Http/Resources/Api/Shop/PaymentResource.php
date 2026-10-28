<?php

namespace App\Http\Resources\Api\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order?->order_number,
            'provider' => $this->provider,
            'mode' => $this->mode,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'provider_reference' => $this->payment_order_id,
            'transaction_reference' => $this->payment_id,
            'refund_id' => $this->refund_id,
            'method' => $this->method,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'refunded_at' => $this->refunded_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
