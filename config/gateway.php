<?php

return [
    'min_deposit_inr' => (float) env('MIN_DEPOSIT_INR', 1),
    'max_deposit_inr' => (float) env('MAX_DEPOSIT_INR', 500000),
    'usd_to_inr_rate' => (float) env('USD_TO_INR_RATE', 100),
    'upi_id' => env('DEPOSIT_UPI_ID', ''),
    'usdt_wallet' => env('USDT_WALLET_ADDRESS', ''),
    'upigateway' => [
        'mode' => env('UPIGATEWAY_MODE', 'test'),
        'key' => env('UPIGATEWAY_KEY', ''),
        'base_url' => env('UPIGATEWAY_BASE_URL', 'https://api.ekqr.in'),
        'ca_bundle' => env('UPIGATEWAY_CA_BUNDLE', ''),
        'modes' => [
            'test' => [
                'key' => env('UPIGATEWAY_TEST_KEY') ?: env('UPIGATEWAY_KEY', ''),
                'base_url' => env('UPIGATEWAY_TEST_BASE_URL') ?: env('UPIGATEWAY_BASE_URL', 'https://api.ekqr.in'),
            ],
            'live' => [
                'key' => env('UPIGATEWAY_LIVE_KEY', ''),
                'base_url' => env('UPIGATEWAY_LIVE_BASE_URL', ''),
            ],
        ],
    ],
    'ipaymentwallet' => [
        'mode' => env('IPAYMENT_MODE', 'test'),
        'enabled' => (bool) env('IPAYMENT_ENABLED', false),
        'url' => env('PAYMENT_GATEWAY_URL', 'https://ipaymentwallet.com/api'),
        'api_key' => env('PAYMENT_GATEWAY_API_KEY', ''),
        'ca_bundle' => env('PAYMENT_GATEWAY_CA_BUNDLE', ''),
        'modes' => [
            'test' => [
                'api_key' => env('IPAYMENT_TEST_API_KEY') ?: env('PAYMENT_GATEWAY_API_KEY', ''),
                'base_url' => env('IPAYMENT_TEST_BASE_URL') ?: env('PAYMENT_GATEWAY_URL') ?: env('IPAYMENT_BASE_URL', 'https://ipaymentwallet.com/api'),
                'webhook_secret' => env('IPAYMENT_TEST_WEBHOOK_SECRET', ''),
            ],
            'live' => [
                'api_key' => env('IPAYMENT_LIVE_API_KEY', ''),
                'base_url' => env('IPAYMENT_LIVE_BASE_URL', ''),
                'webhook_secret' => env('IPAYMENT_LIVE_WEBHOOK_SECRET', ''),
            ],
        ],
    ],
];
