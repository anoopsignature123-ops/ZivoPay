<?php

return [
    'min_deposit_inr' => (float) env('MIN_DEPOSIT_INR', 1),
    'max_deposit_inr' => (float) env('MAX_DEPOSIT_INR', 500000),
    'usd_to_inr_rate' => (float) env('USD_TO_INR_RATE', 100),
    'upi_id' => env('DEPOSIT_UPI_ID', ''),
    'usdt_wallet' => env('USDT_WALLET_ADDRESS', ''),
    'upigateway' => [
        'key' => env('UPIGATEWAY_KEY', ''),
        'base_url' => env('UPIGATEWAY_BASE_URL', 'https://api.ekqr.in'),
        'ca_bundle' => env('PAYMENT_GATEWAY_CA_BUNDLE', ''),
    ],
    'ipaymentwallet' => [
        'enabled' => (bool) env('IPAYMENT_ENABLED', false),
        'url' => env('PAYMENT_GATEWAY_URL', 'https://ipaymentwallet.com/api'),
        'api_key' => env('PAYMENT_GATEWAY_API_KEY', ''),
        'ca_bundle' => env('PAYMENT_GATEWAY_CA_BUNDLE', ''),
    ],
];
