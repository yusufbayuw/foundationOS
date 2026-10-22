<?php

/**
 * WebAuthn passkeys are disabled until Issue #75 security acceptance.
 *
 * Origins and RP IDs are server configured. Never derive them from
 * untrusted Host / X-Forwarded-Host request headers.
 */
return [
    'enabled' => (bool) env('PASSKEYS_ENABLED', false),
    'relying_party_id' => env('PASSKEYS_RP_ID'),
    'allowed_origins' => array_values(array_filter([env('PASSKEYS_ORIGIN')])),
    'user_handle_secret' => env('PASSKEYS_USER_HANDLE_SECRET', env('APP_KEY')),
    'timeout' => 60000,
    'guard' => 'web',
    'middleware' => ['web'],
    'management_middleware' => ['password.confirm'],
    'throttle' => 'throttle:6,1',
    'redirect' => '/admin',
];
