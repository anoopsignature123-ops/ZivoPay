<?php

return [
    'gateway_url' => env('SMS_GATEWAY_URL', 'https://sms.textifydigitals.in/vb/apikey.php'),
    'api_key' => env('SMS_API_KEY', 'oyiF8hqbfiPYNddW'),
    'sender_id' => env('SMS_SENDER_ID', 'ZIVOFS'),

    'templates' => [
        'register' => [
            'id' => env('SMS_DLT_TE_ID_REGISTER', '1777179516697440701'),
        ],
        'otp' => [
            'id' => env('SMS_DLT_TE_ID_OTP', '1777179507319570437'),
        ],
    ],
];
