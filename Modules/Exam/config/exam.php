<?php

return [
    'runtime' => [
        'base_url' => env('EXAM_RUNTIME_BASE_URL', 'https://exam-lite.example.com'),
        'webhook_secret' => env('EXAM_RUNTIME_WEBHOOK_SECRET'),
    ],
    'control_room' => [
        'base_url' => env('EXAM_CONTROL_ROOM_BASE_URL', 'https://exam-control-room.example.com'),
    ],
];
