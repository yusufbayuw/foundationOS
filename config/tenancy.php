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
    | Fail-closed tenant scope (HTTP + explicit test runs)
    |--------------------------------------------------------------------------
    |
    | When true, Eloquent queries on BelongsToTenant models without CurrentTenant
    | bound will throw instead of returning all rows.
    |
    | Defaults to true when APP_ENV=production. Artisan/queue (non-test console)
    | stay fail-open so seeders and jobs can use withoutTenantScope() explicitly.
    |
    | Override locally: TENANCY_SCOPE_FAIL_CLOSED=false in .env
    |
    */
    'scope_fail_closed' => filter_var(
        env('TENANCY_SCOPE_FAIL_CLOSED', env('APP_ENV') === 'production'),
        FILTER_VALIDATE_BOOL,
    ),

    /*
    |--------------------------------------------------------------------------
    | Stale Moodle outbox processing recovery
    |--------------------------------------------------------------------------
    */
    'moodle_processing_stale_minutes' => (int) env('MOODLE_PROCESSING_STALE_MINUTES', 30),

];
