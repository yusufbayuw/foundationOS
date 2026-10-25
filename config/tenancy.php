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

    /*
    | Human API tokens must retain active tenant membership at request time.
    | Production default is strict; nonproduction fixtures can opt in explicitly.
    */
    'api_enforce_membership' => filter_var(
        env('TENANCY_API_ENFORCE_MEMBERSHIP', env('APP_ENV') === 'production'),
        FILTER_VALIDATE_BOOL,
    ),

    // Never enable this on real HTTP traffic; only existing Sanctum::actingAs tests.
    'api_allow_transient_test_token' => true,

    /*
    | Compatibility allowlist for two legacy system dispatch jobs.
    | Framework queued listeners are NOT allowed here: WithTenantContext
    | explicitly verifies the underlying listener, event and tenant instead.
    | No stale tenant or permission team may be inherited from an earlier job.
    */
    'queue_tenantless_job_types' => [
        'App\\Jobs\\ProcessMoodleSyncOutboxJob',
        'Modules\\Workflow\\Jobs\\CheckWorkflowSlaJob',
    ],

    /*
    | Finite trial for new self-registered tenants. Existing tenant records
    | and paid subscriptions are not changed.
    */
    'self_registration_trial_days' => max(1, (int) env('TENANCY_SELF_REGISTRATION_TRIAL_DAYS', 14)),

];
