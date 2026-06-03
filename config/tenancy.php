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
    | Fail-closed tenant scope (HTTP only)
    |--------------------------------------------------------------------------
    |
    | When true, Eloquent queries on BelongsToTenant models without CurrentTenant
    | bound will throw instead of returning all rows. Console and PHPUnit are
    | always exempt so seeders and tests keep working.
    |
    */
    'scope_fail_closed' => env('TENANCY_SCOPE_FAIL_CLOSED', false),

    /*
    |--------------------------------------------------------------------------
    | Stale Moodle outbox processing recovery
    |--------------------------------------------------------------------------
    */
    'moodle_processing_stale_minutes' => (int) env('MOODLE_PROCESSING_STALE_MINUTES', 30),

];
