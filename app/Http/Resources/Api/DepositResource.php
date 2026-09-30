<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
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
            'deposit_ref' => $this->deposit_ref,
            'amount' => (float) $this->amount,
            'charge' => (float) $this->charge,
            'final_amount' => (float) $this->final_amount,
            'payment_method' => $this->payment_method,
            'trx_hash' => $this->trx_hash,
            'proof_file' => $this->proof_file,
            'proof_file_url' => $this->proof_file ? asset('storage/'.$this->proof_file) : null,
            // 'status' => ucfirst($this->status),
            'admin_remark' => $this->admin_remark,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}