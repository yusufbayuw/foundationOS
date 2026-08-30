<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'foundationos' => [
        'messaging' => [
            'channels' => [
                'whatsapp' => env('FOS_WHATSAPP_ENABLED', false),
                'sms' => env('FOS_SMS_ENABLED', false),
                'push' => env('FOS_PUSH_ENABLED', false),
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
                'whatsapp_local_secret' => env('FOS_WHATSAPP_LOCAL_WEBHOOK_SECRET'),
            ],
        ],
    ],

];
