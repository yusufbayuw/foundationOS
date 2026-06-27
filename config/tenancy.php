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
    | Fail-closed tenant scope (PHPUnit + optional CLI strictness)
    |--------------------------------------------------------------------------
    |
    | HTTP requests always fail-closed when tenant context is missing.
    |
    | When true, PHPUnit and tenant:run contexts also throw when querying
    | BelongsToTenant models without CurrentTenant bound.
    |
    | Real Artisan/queue console stays fail-open so seeders and cross-tenant
    | commands can use withoutTenantScope() explicitly.
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
