<?php

namespace App\Services\Shop;

use App\Models\PaymentGatewaySetting;

class ShopPaymentConfiguration
{
    /** @return array<string, bool|string|null> */
    public function forApp(): array
    {
        $gateway = PaymentGatewaySetting::razorpay();
        $enabled = $gateway->isReady();

        return [
            'enabled' => $enabled,
            'provider' => 'razorpay',
            'mode' => $gateway->mode,
            'key_id' => $enabled ? $gateway->keyId() : null,
            'currency' => 'INR',
            'checkout_url' => route('api.shop.checkout'),
            'verify_url' => route('api.shop.payments.verify'),
            'history_url' => route('api.shop.payments.history'),
            'webhook_url' => route('api.shop.payments.webhook'),
        ];
    }
}
