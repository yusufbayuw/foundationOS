<?php

use App\Services\Workflow\StaticMultiUserResolver;
use Modules\Workflow\Support\JsonLogicEvaluator;

return [
    'name' => 'Workflow',
    'enabled' => env('WORKFLOW_ENABLED', true),
    'queue' => env('WORKFLOW_QUEUE', 'workflow'),
    'sla_queue' => env('WORKFLOW_SLA_QUEUE', 'workflow-sla'),
    'allowed_subject_types' => [],
    'allowed_assignee_resolvers' => [
        StaticMultiUserResolver::class,
    ],
    'json_logic_class' => JsonLogicEvaluator::class,
    'default_file_disk' => env('WORKFLOW_FILE_DISK', env('FILESYSTEM_DISK', 'local')),
    'max_schema_fields' => (int) env('WORKFLOW_MAX_SCHEMA_FIELDS', 50),
    'tenant_scope_fallback' => (bool) env('WORKFLOW_TENANT_SCOPE_FALLBACK', true),
    'allowed_options_sources' => [
        'static',
    ],
    'allowed_automation_jobs' => [],
];
