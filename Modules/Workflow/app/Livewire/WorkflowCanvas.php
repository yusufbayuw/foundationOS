<?php

namespace Modules\Workflow\Livewire;

use App\Support\CurrentTenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Services\WorkflowCanvasService;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

class WorkflowCanvas extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

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
            $workflow = Workflow::query()->findOrFail($workflowId);
            Gate::authorize('view', $workflow);
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

    public function saveDraftAction(): Action
    {
        return Action::make('saveDraftAction')
            ->label(FilamentUi::text('Save Draft'))
            ->outlined()
            ->authorize(fn (): bool => $this->canMutateWorkflow())
            ->action(function (): void {
                $this->saveDraft();
            });
    }

    public function publishAction(): Action
    {
        return Action::make('publishAction')
            ->label(FilamentUi::text('Publish'))
            ->color('primary')
            ->authorize(fn (): bool => $this->canMutateWorkflow())
            ->requiresConfirmation()
            ->visible(fn (): bool => $this->workflowStatus === 'draft')
            ->action(function (): void {
                $this->publish();
            });
    }

    public function importJsonAction(): Action
    {
        return Action::make('importJsonAction')
            ->label(FilamentUi::text('Import JSON'))
            ->outlined()
            ->authorize('create', Workflow::class)
            ->schema([
                Textarea::make('payload')
                    ->label(FilamentUi::text('Workflow JSON'))
                    ->required()
                    ->rows(14),
            ])
            ->action(function (array $data): void {
                $this->importJsonPayload((string) $data['payload']);
            });
    }

    public function exportJsonAction(): Action
    {
        return Action::make('exportJsonAction')
            ->label(FilamentUi::text('Export JSON'))
            ->outlined()
            ->authorize(fn (): bool => $this->canViewWorkflow())
            ->visible(fn (): bool => filled($this->workflowId))
            ->action(function (): void {
                $this->exportJson();
            });
    }

    public function addStepAction(): Action
    {
        return Action::make('addStepAction')
            ->label(FilamentUi::text('Add Step'))
            ->outlined()
            ->color('primary')
            ->authorize(fn (): bool => $this->canMutateWorkflow())
            ->action(function (): void {
                $this->addStep();
            });
    }

    public function deleteStepAction(): Action
    {
        return Action::make('deleteStepAction')
            ->label(FilamentUi::text('Delete Step'))
            ->color('danger')
            ->authorize(fn (): bool => $this->canMutateWorkflow())
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $uuid = $arguments['uuid'] ?? null;

                if (! is_string($uuid)) {
                    return;
                }

                $this->deleteStep($uuid);
            });
    }

    public function saveDraft(): void
    {
        $this->authorizeWorkflowMutation();

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
        $this->authorizeWorkflowMutation();

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
        Gate::authorize('view', $workflow);
        $payload = app(WorkflowCanvasService::class)->exportPayload($workflow);

        $this->dispatch('workflow-canvas:download-json', payload: $payload, filename: "workflow_{$workflow->code}_v{$workflow->version}.json");
    }

    public function importJsonPayload(string $payload): void
    {
        Gate::authorize('create', Workflow::class);

        $tenantId = app(CurrentTenant::class)->id();

        if (! $tenantId) {
            Notification::make()->danger()->title('No tenant context')->send();

            return;
        }

        $decoded = json_decode($payload, true);

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

    private function canMutateWorkflow(): bool
    {
        if (! $this->workflowId) {
            return Gate::allows('create', Workflow::class);
        }

        $workflow = Workflow::query()->find($this->workflowId);

        return $workflow instanceof Workflow && Gate::allows('update', $workflow);
    }

    private function canViewWorkflow(): bool
    {
        if (! $this->workflowId) {
            return false;
        }

        $workflow = Workflow::query()->find($this->workflowId);

        return $workflow instanceof Workflow && Gate::allows('view', $workflow);
    }

    private function authorizeWorkflowMutation(): void
    {
        if (! $this->workflowId) {
            Gate::authorize('create', Workflow::class);

            return;
        }

        Gate::authorize('update', Workflow::query()->findOrFail($this->workflowId));
    }

    public function render(): View
    {
        return view('workflow::livewire.workflow-canvas');
    }
}
