<?php

return [
    'enabled' => (bool) env('MOODLE_SYNC_ENABLED', false),
    'readonly' => (bool) env('MOODLE_SYNC_READONLY', false),
    'base_url' => env('MOODLE_BASE_URL'),
    'token' => env('MOODLE_WS_TOKEN'),
    'wsformat' => env('MOODLE_WS_FORMAT', 'json'),
    'timeout' => (int) env('MOODLE_TIMEOUT', 15),
    'verify_ssl' => (bool) env('MOODLE_VERIFY_SSL', true),
    'category_parent_id' => env('MOODLE_CATEGORY_PARENT_ID'),
    'enrol_role_id' => (int) env('MOODLE_ENROL_ROLE_ID', 5),
    'cohort_sync_enabled' => (bool) env('MOODLE_COHORT_SYNC_ENABLED', true),
    'role_map' => [
        'student' => (int) env('MOODLE_ROLE_STUDENT', 5),
        'teacher' => (int) env('MOODLE_ROLE_TEACHER', 3),
        'manager' => (int) env('MOODLE_ROLE_MANAGER', 1),
    ],
    'calendar_sync_enabled' => (bool) env('MOODLE_CALENDAR_SYNC_ENABLED', false),
    'learning_pull_enabled' => (bool) env('MOODLE_LEARNING_PULL_ENABLED', false),
    'attendance_pull_enabled' => (bool) env('MOODLE_ATTENDANCE_PULL_ENABLED', false),
    'queue' => env('MOODLE_SYNC_QUEUE', 'moodle-sync'),
    'max_attempts' => (int) env('MOODLE_SYNC_MAX_ATTEMPTS', 7),
    'batch_limit' => (int) env('MOODLE_SYNC_BATCH_LIMIT', 100),
    'backoff_minutes' => [1, 2, 5, 10, 20, 30, 60],
];
