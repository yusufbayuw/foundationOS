<?php

return [
    'runtime' => [
        'base_url' => env('EXAM_RUNTIME_BASE_URL'),
        'api_key' => env('EXAM_RUNTIME_API_KEY'),
        'hmac_secret' => env('EXAM_RUNTIME_HMAC_SECRET'),
        'webhook_secret' => env('EXAM_RUNTIME_WEBHOOK_SECRET'),
        'timeout' => (int) env('EXAM_RUNTIME_TIMEOUT', 30),
    ],
    'control_room' => [
        'base_url' => env('EXAM_CONTROL_ROOM_BASE_URL', 'https://exam-control-room.example.com'),
    ],
];
