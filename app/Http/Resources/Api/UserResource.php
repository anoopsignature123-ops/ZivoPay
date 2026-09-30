<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'referral_code' => $this->referral_code,
            'sponsor_code' => $this->sponsor_code,
            'wallet_address' => $this->wallet_address,
            'image' => $this->image,
            'image_url' => $this->image ? asset('storage/'.$this->image) : null,
            'status' => $this->status,
            'kyc_status' => $this->kyc_status ?? 'pending',
            'is_subscription_active' => (bool) $this->is_subscription_active,
            'wallets' => [
                'fund_wallet' => (float) $this->deposit_wallet,
                'earning_wallet' => (float) $this->earning_wallet,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
