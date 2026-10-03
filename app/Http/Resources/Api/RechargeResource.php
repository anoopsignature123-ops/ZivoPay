<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RechargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'service_type' => ucfirst($this->service_type),
            'operator_code' => $this->operator_code,
            'operator_name' => $this->operator_name,
            'circle_code' => $this->circle_code,
            'number' => $this->number,
            'value1' => $this->value1,
            'value2' => $this->value2,
            'amount' => (float) $this->amount,
            'status' => ucfirst($this->status),
            'txid' => $this->txid,
            'opid' => $this->opid,
            'admin_remark' => $this->admin_remark,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
