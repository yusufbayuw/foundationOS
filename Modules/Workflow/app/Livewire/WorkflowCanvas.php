<?php

namespace Modules\Workflow\Livewire;

use App\Support\CurrentTenant;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;
use Modules\Workflow\Services\WorkflowDefinitionPorter;

class WorkflowCanvas extends Component
{
    public ?int $workflowId = null;

    /** @var array<int, array<string, mixed>> */
    public array $steps = [];

    /** @var array<int, array<string, mixed>> */
    public array $transitions = [];

    public ?string $selectedStepUuid = null;

    public ?int $selectedTransitionIndex = null;

    public bool $isDirty = false;

    // Workflow metadata
    public string $workflowCode = '';

    public string $workflowName = '';

    public string $workflowDescription = '';

    public string $workflowStatus = 'draft';

    public function mount(?int $workflowId = null): void
    {
        $this->workflowId = $workflowId;

        if ($workflowId) {
            $this->loadWorkflow($workflowId);
        }
    }

    private function loadWorkflow(int $workflowId): void
    {
        $workflow = Workflow::with(['steps' => fn ($q) => $q->orderBy('sort_order'), 'transitions'])->find($workflowId);

        if (! $workflow) {
            return;
        }

        $this->workflowCode = $workflow->code;
        $this->workflowName = $workflow->name;
        $this->workflowDescription = $workflow->description ?? '';
        $this->workflowStatus = $workflow->status?->value ?? 'draft';

        $this->steps = $workflow->steps->map(fn (WorkflowStep $step) => [
            'uuid' => $step->uuid,
            'code' => $step->code,
            'name' => $step->name,
            'description' => $step->description ?? '',
            'step_type' => $step->step_type?->value ?? 'task',
            'gateway_type' => $step->gateway_type?->value ?? 'none',
            'quorum_strategy' => $step->quorum_strategy?->value,
            'quorum_value' => $step->quorum_value,
            'assignee_type' => $step->assignee_type?->value ?? 'user',
            'assignee_value' => $step->assignee_value ?? '',
            'assignee_config' => $step->assignee_config ?? [],
            'form_schema' => $step->form_schema ?? [],
            'sla_hours' => $step->sla_hours,
            'is_initial' => (bool) $step->is_initial,
            'is_terminal' => (bool) $step->is_terminal,
            'sort_order' => $step->sort_order ?? 0,
            'canvas_position' => $step->canvas_position ?? null,
        ])->values()->all();

        $stepUuidToIndex = collect($this->steps)->pluck('uuid')->flip()->all();

        $this->transitions = $workflow->transitions->map(function (WorkflowTransition $t) use ($workflow) {
            $fromUuid = $workflow->steps->firstWhere('id', $t->from_step_id)?->uuid;
            $toUuid = $t->to_step_id ? $workflow->steps->firstWhere('id', $t->to_step_id)?->uuid : null;

            return [
                'from_uuid' => $fromUuid,
                'to_uuid' => $toUuid,
                'action_name' => $t->action_name,
                'priority' => $t->priority ?? 0,
                'is_default' => (bool) $t->is_default,
                'condition_rules' => $t->condition_rules ?? [],
            ];
        })->values()->all();
    }

    public function addStep(array $payload = []): void
    {
        $this->steps[] = [
            'uuid' => (string) Str::uuid(),
            'code' => $payload['code'] ?? 'step_'.Str::random(4),
            'name' => $payload['name'] ?? 'New Step',
            'description' => '',
            'step_type' => $payload['step_type'] ?? 'task',
            'gateway_type' => 'none',
            'quorum_strategy' => null,
            'quorum_value' => null,
            'assignee_type' => 'user',
            'assignee_value' => '',
            'assignee_config' => [],
            'form_schema' => [],
            'sla_hours' => null,
            'is_initial' => empty($this->steps),
            'is_terminal' => false,
            'sort_order' => count($this->steps),
            'canvas_position' => $payload['canvas_position'] ?? null,
        ];

        $this->isDirty = true;
        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    public function updateStep(string $uuid, array $payload): void
    {
        foreach ($this->steps as $i => $step) {
            if ($step['uuid'] === $uuid) {
                $this->steps[$i] = array_merge($step, $payload, ['uuid' => $uuid]);
                break;
            }
        }

        $this->isDirty = true;
        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    public function deleteStep(string $uuid): void
    {
        $this->steps = array_values(array_filter($this->steps, fn ($s) => $s['uuid'] !== $uuid));
        $this->transitions = array_values(array_filter(
            $this->transitions,
            fn ($t) => $t['from_uuid'] !== $uuid && $t['to_uuid'] !== $uuid
        ));

        if ($this->selectedStepUuid === $uuid) {
            $this->selectedStepUuid = null;
        }

        $this->isDirty = true;
        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    public function addTransition(array $payload): void
    {
        $this->transitions[] = [
            'from_uuid' => $payload['from_uuid'],
            'to_uuid' => $payload['to_uuid'] ?? null,
            'action_name' => $payload['action_name'] ?? 'proceed',
            'priority' => count($this->transitions),
            'is_default' => $payload['is_default'] ?? false,
            'condition_rules' => [],
        ];

        $this->isDirty = true;
        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    public function updateTransition(int $index, array $payload): void
    {
        if (isset($this->transitions[$index])) {
            $this->transitions[$index] = array_merge($this->transitions[$index], $payload);
            $this->isDirty = true;
        }
    }

    public function deleteTransition(int $index): void
    {
        unset($this->transitions[$index]);
        $this->transitions = array_values($this->transitions);

        if ($this->selectedTransitionIndex === $index) {
            $this->selectedTransitionIndex = null;
        }

        $this->isDirty = true;
        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    public function updateStepPosition(string $uuid, float $x, float $y): void
    {
        foreach ($this->steps as $i => $step) {
            if ($step['uuid'] === $uuid) {
                $this->steps[$i]['canvas_position'] = ['x' => $x, 'y' => $y];
                break;
            }
        }

        $this->isDirty = true;
    }

    public function selectStep(string $uuid): void
    {
        $this->selectedStepUuid = $uuid;
        $this->selectedTransitionIndex = null;
    }

    public function selectTransition(int $index): void
    {
        $this->selectedTransitionIndex = $index;
        $this->selectedStepUuid = null;
    }

    public function saveDraft(): void
    {
        $tenantId = app(CurrentTenant::class)->id();

        if (! $tenantId) {
            Notification::make()->danger()->title('No tenant context')->send();

            return;
        }

        $this->persistToDatabase($tenantId);

        $this->isDirty = false;

        Notification::make()
            ->success()
            ->title('Draft saved')
            ->send();
    }

    public function publish(): void
    {
        if (! $this->workflowId) {
            $this->saveDraft();
        }

        if (! $this->workflowId) {
            return;
        }

        $workflow = Workflow::findOrFail($this->workflowId);

        try {
            app(WorkflowDefinitionLifecycleService::class)->publish($workflow, auth()->id());
            $this->workflowStatus = 'active';

            Notification::make()
                ->success()
                ->title('Workflow published')
                ->send();
        } catch (WorkflowConfigurationException $e) {
            Notification::make()
                ->danger()
                ->title('Cannot publish')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function exportJson(): void
    {
        if (! $this->workflowId) {
            return;
        }

        $workflow = Workflow::findOrFail($this->workflowId);
        $payload = app(WorkflowDefinitionPorter::class)->export($workflow);

        $this->dispatch('workflow-canvas:download-json', payload: $payload, filename: "workflow_{$workflow->code}_v{$workflow->version}.json");
    }

    private function persistToDatabase(int $tenantId): void
    {
        DB::transaction(function () use ($tenantId) {
            if ($this->workflowId) {
                $workflow = Workflow::findOrFail($this->workflowId);

                // If active, create a new draft version first
                if ($workflow->status === WorkflowDefinitionStatus::Active) {
                    $workflow = app(WorkflowDefinitionLifecycleService::class)->duplicateAsNewVersion($workflow, auth()->id());
                    $this->workflowId = $workflow->id;
                    $this->workflowStatus = 'draft';
                }

                // Update metadata
                $workflow->update([
                    'name' => $this->workflowName ?: $workflow->name,
                    'description' => $this->workflowDescription ?: $workflow->description,
                ]);

                // Delete transitions first (FK reference to steps), then steps
                $workflow->transitions()->forceDelete();
                $workflow->steps()->forceDelete();
            } else {
                $code = $this->workflowCode ?: 'wf_'.Str::random(6);
                $version = (int) Workflow::query()->where('tenant_id', $tenantId)->where('code', $code)->max('version') + 1;

                $workflow = Workflow::create([
                    'tenant_id' => $tenantId,
                    'code' => $code,
                    'name' => $this->workflowName ?: 'New Workflow',
                    'description' => $this->workflowDescription ?: null,
                    'version' => $version,
                    'status' => WorkflowDefinitionStatus::Draft,
                    'is_active' => false,
                ]);

                $this->workflowId = $workflow->id;
                $this->workflowStatus = 'draft';
            }

            // Remap UUIDs so we never re-use IDs that belong to a different workflow version
            $oldToNewUuid = [];
            $stepsWithNewUuids = [];
            foreach ($this->steps as $stepData) {
                $newUuid = (string) Str::uuid();
                $oldToNewUuid[$stepData['uuid']] = $newUuid;
                $stepsWithNewUuids[] = array_merge($stepData, ['uuid' => $newUuid]);
            }

            $uuidToId = [];

            foreach ($stepsWithNewUuids as $order => $stepData) {
                $step = WorkflowStep::create([
                    'workflow_id' => $workflow->id,
                    'uuid' => $stepData['uuid'],
                    'code' => $stepData['code'],
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?: null,
                    'step_type' => $stepData['step_type'],
                    'gateway_type' => $stepData['gateway_type'] ?? 'none',
                    'quorum_strategy' => $stepData['quorum_strategy'] ?: null,
                    'quorum_value' => $stepData['quorum_value'] ?: null,
                    'assignee_type' => $stepData['assignee_type'],
                    'assignee_value' => $stepData['assignee_value'] ?: null,
                    'assignee_config' => $stepData['assignee_config'] ?: null,
                    'form_schema' => $stepData['form_schema'] ?: null,
                    'sla_hours' => $stepData['sla_hours'] ?: null,
                    'is_initial' => (bool) $stepData['is_initial'],
                    'is_terminal' => (bool) $stepData['is_terminal'],
                    'sort_order' => $order,
                    'canvas_position' => $stepData['canvas_position'] ?: null,
                ]);

                $uuidToId[$stepData['uuid']] = $step->id;
            }

            foreach ($this->transitions as $t) {
                $fromUuid = $oldToNewUuid[$t['from_uuid']] ?? $t['from_uuid'];
                $toUuid = isset($t['to_uuid']) ? ($oldToNewUuid[$t['to_uuid']] ?? $t['to_uuid']) : null;

                WorkflowTransition::create([
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $uuidToId[$fromUuid] ?? null,
                    'to_step_id' => $toUuid ? ($uuidToId[$toUuid] ?? null) : null,
                    'action_name' => $t['action_name'],
                    'priority' => $t['priority'] ?? 0,
                    'is_default' => (bool) $t['is_default'],
                    'condition_rules' => $t['condition_rules'] ?: null,
                ]);
            }
        });
    }

    public function render(): View
    {
        return view('workflow::livewire.workflow-canvas');
    }
}
