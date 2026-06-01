<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API tenant requirement
    |--------------------------------------------------------------------------
    |
    | When true, Sanctum tokens used on tenant-scoped API routes must include
    | tenant_id. Prevents fail-open queries without CurrentTenant context.
    |
    */
    'api_require_tenant' => env('TENANCY_API_REQUIRE_TENANT', true),

    /*
    |--------------------------------------------------------------------------
    | Stale Moodle outbox processing recovery
    |--------------------------------------------------------------------------
    */
    'moodle_processing_stale_minutes' => (int) env('MOODLE_PROCESSING_STALE_MINUTES', 30),

];
