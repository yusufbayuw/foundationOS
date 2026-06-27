<?php

namespace Modules\Workflow\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowInboxPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/workflow/inbox';

    protected string $view = 'workflow::filament.pages.workflow-inbox';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return 'Workflow Worklist';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Workflow');
    }

    /**
     * @return array{modules: list<string>, workflows: array<int, string>}
     */
    public function getFilterOptions(): array
    {
        $query = $this->baseAssignmentsQuery();
        $moduleQuery = clone $query;
        $workflowQuery = clone $query;

        return [
            'modules' => array_values($moduleQuery
                ->join('workflow_instances', 'workflow_instances.id', '=', 'workflow_assignments.workflow_instance_id')
                ->join('workflows', 'workflows.id', '=', 'workflow_instances.workflow_id')
                ->whereNotNull('workflows.module')
                ->distinct()
                ->orderBy('workflows.module')
                ->pluck('workflows.module')
                ->map(fn (mixed $module): string => (string) $module)
                ->all()),
            'workflows' => $workflowQuery
                ->join('workflow_instances', 'workflow_instances.id', '=', 'workflow_assignments.workflow_instance_id')
                ->join('workflows', 'workflows.id', '=', 'workflow_instances.workflow_id')
                ->distinct()
                ->orderBy('workflows.name')
                ->pluck('workflows.name', 'workflows.id')
                ->mapWithKeys(fn (mixed $name, mixed $id): array => [(int) $id => (string) $name])
                ->all(),
        ];
    }

    /**
     * @return array<int, WorkflowAssignment>
     */
    public function getMyTasks(): array
    {
        return $this->filteredPendingAssignments()
            ->latest('assigned_at')
            ->limit(25)
            ->get()
            ->all();
    }

    /**
     * @return array<int, WorkflowAssignment>
     */
    public function getOverdueTasks(): array
    {
        return $this->filteredPendingAssignments()
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->limit(25)
            ->get()
            ->all();
    }

    /**
     * @return array<int, WorkflowAssignment>
     */
    public function getDelegatedTasks(): array
    {
        return $this->filteredPendingAssignments()
            ->whereNotNull('meta->reassigned_by')
            ->latest('assigned_at')
            ->limit(25)
            ->get()
            ->all();
    }

    /**
     * @return array<int, WorkflowAssignment>
     */
    public function getCompletedRecently(): array
    {
        return $this->baseAssignmentsQuery()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(7))
            ->latest('completed_at')
            ->limit(25)
            ->get()
            ->all();
    }

    /**
     * @return Builder<WorkflowAssignment>
     */
    protected function filteredPendingAssignments(): Builder
    {
        return $this->applyFilters(
            $this->baseAssignmentsQuery()->where('status', 'pending')
        );
    }

    /**
     * @return Builder<WorkflowAssignment>
     */
    protected function baseAssignmentsQuery(): Builder
    {
        $user = auth()->user();
        $tenant = Filament::getTenant();

        return WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($user, fn (Builder $query) => $query->where('assigned_to_id', $user->getAuthIdentifier()))
            ->when(! $user, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($tenant, function (Builder $query) use ($tenant): void {
                $query->whereHas('instance', function (Builder $inner) use ($tenant): void {
                    /** @var Builder<WorkflowInstance> $inner */
                    $inner->where($inner->getModel()->qualifyColumn('tenant_id'), $tenant->getKey());
                });
            });
    }

    /**
     * @param  Builder<WorkflowAssignment>  $query
     * @return Builder<WorkflowAssignment>
     */
    protected function applyFilters(Builder $query): Builder
    {
        $module = request()->string('module')->toString();
        $workflowId = request()->integer('workflow_id');
        $sla = request()->string('sla')->toString();

        $query
            ->when($module !== '', function (Builder $inner) use ($module): void {
                $inner->whereHas('instance.workflow', function (Builder $workflowQuery) use ($module): void {
                    /** @var Builder<Workflow> $workflowQuery */
                    $workflowQuery->where('module', $module);
                });
            })
            ->when($workflowId > 0, function (Builder $inner) use ($workflowId): void {
                $inner->whereHas('instance', function (Builder $instanceQuery) use ($workflowId): void {
                    /** @var Builder<WorkflowInstance> $instanceQuery */
                    $instanceQuery->where('workflow_id', $workflowId);
                });
            });

        if ($sla === 'due_today') {
            $query->whereDate('due_at', now()->toDateString());
        }

        if ($sla === 'overdue') {
            $query->whereNotNull('due_at')->where('due_at', '<', now());
        }

        return $query;
    }

    /**
     * @return array<string, string>
     */
    public function summarizeAssignment(WorkflowAssignment $assignment): array
    {
        $dueAt = $assignment->due_at;

        return [
            'workflow' => $assignment->instance->workflow->name ?? 'Workflow',
            'module' => $assignment->instance->workflow->module ?? '-',
            'step' => $assignment->instance->currentStep->name ?? '-',
            'subject' => $assignment->instance->subject_label ?: '-',
            'requester' => $assignment->instance->requester->name ?? '-',
            'assigned_at' => $assignment->assigned_at->format('Y-m-d H:i'),
            'due_at' => $dueAt?->format('Y-m-d H:i') ?? '-',
            'sla_status' => match (true) {
                ! $dueAt => 'No SLA',
                $dueAt->isPast() => 'Overdue',
                $dueAt->isToday() => 'Due Today',
                default => 'On Track',
            },
        ];
    }
}
