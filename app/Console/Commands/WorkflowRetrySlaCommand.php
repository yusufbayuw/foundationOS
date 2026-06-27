<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Workflow\Jobs\CheckWorkflowSlaJob;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowRetrySlaCommand extends Command
{
    protected $signature = 'fos:workflow:retry-sla
        {--tenant= : Filter tenant_id}
        {--organization= : Filter organization_id}
        {--limit=100 : Max workflow instances to dispatch}
        {--all-due : Include all due instances, not only overdue ones}';

    protected $description = 'Re-dispatch SLA checks for workflow instances that are due or overdue';

    public function handle(): int
    {
        $tenantId = is_numeric($this->option('tenant')) ? (int) $this->option('tenant') : null;
        $organizationId = is_numeric($this->option('organization')) ? (int) $this->option('organization') : null;
        $limit = max(1, (int) $this->option('limit'));
        $allDue = (bool) $this->option('all-due');

        $query = WorkflowInstance::query()
            ->where('status', 'running')
            ->whereNotNull('due_at')
            ->when(! $allDue, fn ($builder) => $builder->where('due_at', '<=', now()))
            ->when($tenantId, fn ($builder) => $builder->where($builder->getModel()->qualifyColumn('tenant_id'), $tenantId))
            ->when($organizationId, fn ($builder) => $builder->where($builder->getModel()->qualifyColumn('organization_id'), $organizationId))
            ->orderBy('due_at')
            ->limit($limit);

        $instances = $query->get(['id']);

        foreach ($instances as $instance) {
            CheckWorkflowSlaJob::dispatch((int) $instance->id);
        }

        $this->info("Dispatched SLA check jobs for {$instances->count()} workflow instance(s).");

        return self::SUCCESS;
    }
}
