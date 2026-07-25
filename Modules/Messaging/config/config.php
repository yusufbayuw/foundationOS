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
