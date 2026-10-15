<?php

return [
    'delivery_fee' => (float) env('SHOP_DELIVERY_FEE', 0),
    'free_delivery_threshold' => (float) env('SHOP_FREE_DELIVERY_THRESHOLD', 999),
    'return_window_days' => (int) env('SHOP_RETURN_WINDOW_DAYS', 7),
];
