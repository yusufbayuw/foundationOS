<?php

namespace Modules\Workflow\Filament\Pages;

use App\Support\TypedValue;
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
                ->map(fn (mixed $module): string => TypedValue::string($module))
                ->all()),
            'workflows' => $workflowQuery
                ->join('workflow_instances', 'workflow_instances.id', '=', 'workflow_assignments.workflow_instance_id')
                ->join('workflows', 'workflows.id', '=', 'workflow_instances.workflow_id')
                ->distinct()
                ->orderBy('workflows.name')
                ->pluck('workflows.name', 'workflows.id')
                ->mapWithKeys(fn (mixed $name, mixed $id): array => [TypedValue::int($id) => TypedValue::string($name)])
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
        $userId = $user?->getAuthIdentifier();
        $tenantId = TypedValue::tenantKey($tenant?->getKey());

        return WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($userId !== null, fn (Builder $query) => $query->where('assigned_to_id', $userId))
            ->when($userId === null, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($tenantId !== null, function (Builder $query) use ($tenantId): void {
                $query->whereHas('instance', function (Builder $inner) use ($tenantId): void {
                    /** @var Builder<WorkflowInstance> $inner */
                    $inner->where($inner->getModel()->qualifyColumn('tenant_id'), $tenantId);
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
        $instance = $assignment->instance;

        return [
            'workflow' => TypedValue::string(data_get($instance, 'workflow.name'), 'Workflow'),
            'module' => TypedValue::string(data_get($instance, 'workflow.module'), '-'),
            'step' => TypedValue::string(data_get($instance, 'currentStep.name'), '-'),
            'subject' => TypedValue::string(data_get($instance, 'subject_label'), '-'),
            'requester' => TypedValue::string(data_get($instance, 'requester.name'), '-'),
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
