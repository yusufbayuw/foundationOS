<?php

return [
    'payment_gateway' => env('DONATION_PAYMENT_GATEWAY', 'midtrans'),

    'gateways' => [
        'midtrans' => [
            'server_key' => env('DONATION_MIDTRANS_SERVER_KEY', env('MIDTRANS_SERVER_KEY', '')),
            'client_key' => env('DONATION_MIDTRANS_CLIENT_KEY', env('MIDTRANS_CLIENT_KEY', '')),
            'is_production' => env('DONATION_MIDTRANS_IS_PRODUCTION', env('MIDTRANS_IS_PRODUCTION', false)),
            'is_sanitized' => env('DONATION_MIDTRANS_IS_SANITIZED', env('MIDTRANS_IS_SANITIZED', true)),
            'is_3ds' => env('DONATION_MIDTRANS_IS_3DS', env('MIDTRANS_IS_3DS', true)),
            'finish_url' => env('DONATION_MIDTRANS_FINISH_URL'),
            'error_url' => env('DONATION_MIDTRANS_ERROR_URL'),
        ],
    ],
];
