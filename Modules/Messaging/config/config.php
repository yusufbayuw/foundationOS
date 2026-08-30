<?php

return [
    'channels' => [
        'database' => true,
        'mail' => true,
        'whatsapp' => config('services.foundationos.messaging.channels.whatsapp', false),
        'sms' => config('services.foundationos.messaging.channels.sms', false),
        'push' => config('services.foundationos.messaging.channels.push', false),
        'telegram' => env('FOS_TELEGRAM_ENABLED', false),
    ],

    'push' => [
        'provider' => config('services.foundationos.messaging.push.provider', 'fcm'),
        'fcm' => [
            'endpoint' => config('services.foundationos.messaging.push.fcm.endpoint', 'https://fcm.googleapis.com/fcm/send'),
            'server_key' => config('services.foundationos.messaging.push.fcm.server_key'),
        ],
    ],

    'webhooks' => [
        'whatsapp_secret' => config('services.foundationos.messaging.webhooks.whatsapp_secret'),
        'whatsapp' => [
            'providers' => [
                'local' => [
                    'secret' => config('services.foundationos.messaging.webhooks.whatsapp_local_secret'),
                ],
            ],
        ],
    ],
];
