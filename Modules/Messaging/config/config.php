<?php

return [
    'channels' => [
        'database' => true,
        'mail' => true,
        'whatsapp' => env('FOS_WHATSAPP_ENABLED', false),
        'sms' => env('FOS_SMS_ENABLED', false),
        'push' => env('FOS_PUSH_ENABLED', false),
        'telegram' => env('FOS_TELEGRAM_ENABLED', false),
    ],

    'push' => [
        'provider' => env('FOS_PUSH_PROVIDER', 'fcm'),
        'fcm' => [
            'endpoint' => env('FOS_FCM_ENDPOINT', 'https://fcm.googleapis.com/fcm/send'),
            'server_key' => env('FOS_FCM_SERVER_KEY'),
        ],
    ],

    'webhooks' => [
        'whatsapp_secret' => env('FOS_WHATSAPP_WEBHOOK_SECRET'),
        'whatsapp' => [
            'providers' => [
                'local' => [
                    'secret' => env('FOS_WHATSAPP_LOCAL_WEBHOOK_SECRET'),
                ],
            ],
        ],
    ],
];
