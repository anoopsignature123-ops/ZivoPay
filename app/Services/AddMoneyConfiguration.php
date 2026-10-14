<?php

namespace App\Services;

use App\Models\PaymentGatewaySetting;

class AddMoneyConfiguration
{
    /**
     * @return array<string, bool|float|string|null>
     */
    public function forApp(): array
    {
        $gateway = PaymentGatewaySetting::razorpay();
        $enabled = $gateway->isReady();

        return [
            'enabled' => $enabled,
            'provider' => 'razorpay',
            'mode' => $gateway->mode,
            'api_key_id' => $enabled ? $gateway->keyId() : null,
            'api_key_secret' => $enabled ? $gateway->keySecret() : null,
            'currency' => 'INR',
            'min_amount' => (float) config('gateway.min_deposit', 100),
            'max_amount' => (float) config('gateway.max_deposit', 500000),
            'start_url' => route('api.add-fund.start'),
            'verify_url' => route('api.add-fund.verify'),
            'status_url' => route('api.add-fund.status'),

        ];
    }
}
