<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Models\Workflow;

class WorkflowHealthCheckCommand extends Command
{
    protected $signature = 'fos:workflow:health-check {--tenant=} {--organization=}';

    protected $description = 'Run workflow definition health checks for active workflows';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');
        $organizationId = $this->option('organization');

        $workflows = Workflow::query()
            ->with(['steps', 'transitions', 'automatedActions'])
            ->where('is_active', true)
            ->when($tenantId, fn ($query) => $query->where('tenant_id', (int) $tenantId))
            ->when($organizationId, fn ($query) => $query->where('organization_id', (int) $organizationId))
            ->get();

        if ($workflows->isEmpty()) {
            $this->warn('No active workflows matched the requested scope.');

            return self::SUCCESS;
        }

        $rows = [];
        $hasFailure = false;

        foreach ($workflows as $workflow) {
            $issues = [];

            if ($workflow->steps->where('is_initial', true)->count() !== 1) {
                $issues[] = 'must have exactly one initial step';
            }

            $nonTerminalWithoutTransitions = $workflow->steps
                ->where('is_terminal', false)
                ->filter(fn ($step) => $workflow->transitions->where('from_step_id', $step->id)->isEmpty())
                ->pluck('code')
                ->all();

            if ($nonTerminalWithoutTransitions !== []) {
                $issues[] = 'missing transitions for: '.implode(', ', $nonTerminalWithoutTransitions);
            }

            $defaultTransitionConflicts = $workflow->transitions
                ->groupBy(fn ($transition) => $transition->from_step_id.':'.$transition->action_name)
                ->filter(fn ($group) => $group->where('is_default', true)->count() > 1)
                ->keys()
                ->all();

            if ($defaultTransitionConflicts !== []) {
                $issues[] = 'multiple default transitions on: '.implode(', ', $defaultTransitionConflicts);
            }

            foreach ($workflow->steps as $step) {
                if ($step->assignee_type === 'resolver') {
                    $resolverClass = (string) (data_get($step->assignee_config, 'resolver_class') ?: $step->assignee_value);
                    $allowedResolvers = (array) config('workflow.allowed_assignee_resolvers', []);

                    if ($resolverClass === '') {
                        $issues[] = "step {$step->code} is missing resolver class";
                    } elseif (! class_exists($resolverClass)) {
                        $issues[] = "step {$step->code} resolver class {$resolverClass} does not exist";
                    } elseif (! in_array($resolverClass, $allowedResolvers, true)) {
                        $issues[] = "step {$step->code} uses non-whitelisted resolver {$resolverClass}";
                    } elseif (! is_subclass_of($resolverClass, WorkflowDynamicAssigneeResolver::class)) {
                        $issues[] = "step {$step->code} resolver {$resolverClass} must implement WorkflowDynamicAssigneeResolver";
                    }
                }

                $schema = Arr::wrap($step->form_schema);

                if (count($schema) > (int) config('workflow.max_schema_fields', 50)) {
                    $issues[] = "step {$step->code} exceeds max schema fields";
                }
            }

            $rows[] = [
                $workflow->id,
                $workflow->name,
                $workflow->tenant_id,
                $workflow->organization_id ?? '-',
                $issues === [] ? 'OK' : 'FAIL',
                $issues === [] ? '-' : implode('; ', $issues),
            ];

            if ($issues !== []) {
                $hasFailure = true;
            }
        }

        $this->table(['ID', 'Workflow', 'Tenant', 'Organization', 'Status', 'Issues'], $rows);

        return $hasFailure ? self::FAILURE : self::SUCCESS;
    }
}
