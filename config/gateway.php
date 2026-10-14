<?php

return [
    'min_deposit' => (float) env('MIN_DEPOSIT_AMOUNT', 100),
    'max_deposit' => (float) env('MAX_DEPOSIT_AMOUNT', 500000),
    'upi_id' => env('DEPOSIT_UPI_ID', ''),
    'usdt_wallet' => env('USDT_WALLET_ADDRESS', ''),
    'razorpay_ca_bundle' => env('RAZORPAY_CA_BUNDLE', ''),
    'razorpay' => [
        'test_key_id' => env('RAZORPAY_TEST_KEY_ID', ''),
        'test_key_secret' => env('RAZORPAY_TEST_KEY_SECRET', ''),
        'test_webhook_secret' => env('RAZORPAY_TEST_WEBHOOK_SECRET', ''),
        'live_key_id' => env('RAZORPAY_LIVE_KEY_ID', ''),
        'live_key_secret' => env('RAZORPAY_LIVE_KEY_SECRET', ''),
        'live_webhook_secret' => env('RAZORPAY_LIVE_WEBHOOK_SECRET', ''),
    ],
];
