<?php

namespace Modules\Workflow\Livewire;

use App\Support\CurrentTenant;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Services\WorkflowCanvasService;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

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

    public bool $showImportModal = false;

    public string $importPayload = '';

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
        $canvas = app(WorkflowCanvasService::class)->canvasForWorkflow($workflowId);

        if (! $canvas) {
            return;
        }

        $this->workflowCode = $canvas['workflow']['code'];
        $this->workflowName = $canvas['workflow']['name'];
        $this->workflowDescription = $canvas['workflow']['description'];
        $this->workflowStatus = $canvas['workflow']['status'];
        $this->steps = $canvas['steps'];
        $this->transitions = $canvas['transitions'];
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
        $payload = app(WorkflowCanvasService::class)->exportPayload($workflow);

        $this->dispatch('workflow-canvas:download-json', payload: $payload, filename: "workflow_{$workflow->code}_v{$workflow->version}.json");
    }

    public function openImportModal(): void
    {
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importPayload = '';
    }

    public function importFromJson(): void
    {
        $tenantId = app(CurrentTenant::class)->id();

        if (! $tenantId) {
            Notification::make()->danger()->title('No tenant context')->send();

            return;
        }

        $decoded = json_decode($this->importPayload, true);

        if (! is_array($decoded)) {
            Notification::make()
                ->danger()
                ->title('Invalid JSON')
                ->body('Payload must be valid JSON.')
                ->send();

            return;
        }

        $canvasService = app(WorkflowCanvasService::class);
        $errors = $canvasService->validateImportPayload($decoded);

        if ($errors !== []) {
            Notification::make()
                ->danger()
                ->title('Import validation failed')
                ->body(implode("\n", array_slice($errors, 0, 5)))
                ->send();

            return;
        }

        try {
            $workflow = $canvasService->importPayload($decoded, $tenantId);
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->danger()
                ->title('Import failed')
                ->body($e->getMessage())
                ->send();

            return;
        }

        $this->workflowId = $workflow->id;
        $this->loadWorkflow($workflow->id);
        $this->isDirty = false;
        $this->closeImportModal();

        Notification::make()
            ->success()
            ->title('Workflow imported as draft')
            ->send();

        $this->dispatch('workflow-canvas:state-updated', steps: $this->steps, transitions: $this->transitions);
    }

    private function persistToDatabase(int $tenantId): void
    {
        $workflow = app(WorkflowCanvasService::class)->persistCanvas(
            tenantId: $tenantId,
            workflowId: $this->workflowId,
            workflowCode: $this->workflowCode,
            workflowName: $this->workflowName,
            workflowDescription: $this->workflowDescription,
            steps: $this->steps,
            transitions: $this->transitions,
            actorId: auth()->id(),
        );

        $this->workflowId = $workflow->id;
        $this->workflowStatus = $workflow->status?->value ?? 'draft';
    }

    public function render(): View
    {
        return view('workflow::livewire.workflow-canvas');
    }
}
