<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Support\WorkflowContextData;

class ViewWorkflowInstance extends ViewRecord
{
    protected static string $resource = WorkflowInstanceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var WorkflowInstance $record */
        $record = $this->getRecord()->loadMissing(['currentStep', 'assignments']);
        $step = $record->currentStep;

        if (! $step || ! $this->hasPendingAssignment($record)) {
            return [];
        }

        $actions = [];

        foreach ($this->getAvailableActionNames($record) as $actionName) {
            $actions[] = Action::make($actionName)
                ->label((string) str($actionName)->headline())
                ->color($this->resolveActionColor($record, $actionName))
                ->form($actionName === 'cancel' ? [] : $this->buildDynamicFormSchema($record))
                ->requiresConfirmation($actionName === 'cancel' || $actionName === 'reject')
                ->action(function (array $data) use ($record, $actionName): void {
                    /** @var User $actor */
                    $actor = auth()->user();
                    $engine = app(WorkflowEngine::class);

                    if ($actionName === 'cancel') {
                        $engine->cancel($record, $actor, Arr::get($data, 'workflow_note'));
                    } else {
                        $engine->advance($record, $actionName, $data, $actor, Arr::get($data, 'workflow_note'));
                    }

                    $this->record = $record->fresh();

                    Notification::make()
                        ->title('Workflow berhasil diperbarui.')
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }

    protected function buildDynamicFormSchema(WorkflowInstance $record): array
    {
        /** @var RuleEngine $ruleEngine */
        $ruleEngine = app(RuleEngine::class);
        $evaluatedSchema = $ruleEngine->evaluateFieldState(
            $record->currentStep?->form_schema ?? [],
            WorkflowContextData::fromInstance($record),
        );

        $schema = [];

        foreach ($evaluatedSchema as $field) {
            $name = (string) ($field['name'] ?? '');

            if ($name === '' || ! ($field['_visible'] ?? true)) {
                continue;
            }

            $component = match ((string) ($field['type'] ?? 'text')) {
                'textarea' => Textarea::make($name),
                'number' => TextInput::make($name)->numeric(),
                'date' => DatePicker::make($name),
                'select', 'radio' => Select::make($name)->options($field['options'] ?? [])->searchable(),
                'file' => FileUpload::make($name)->disk(config('workflow.default_file_disk')),
                default => TextInput::make($name),
            };

            if (($field['type'] ?? null) === 'file' && ! empty($field['accepted_types']) && is_array($field['accepted_types'])) {
                $component->acceptedFileTypes(array_map(
                    fn (string $type): string => str_contains($type, '/') ? $type : '.' . ltrim($type, '.'),
                    $field['accepted_types'],
                ));
            }

            if (($field['type'] ?? null) === 'file' && ! empty($field['max_size_kb'])) {
                $component->maxSize((int) $field['max_size_kb']);
            }

            $schema[] = $component
                ->label((string) ($field['label'] ?? str($name)->headline()))
                ->placeholder((string) ($field['placeholder'] ?? ''))
                ->helperText($field['help_text'] ?? null)
                ->default($field['default_value'] ?? null)
                ->disabled((bool) ($field['_disabled'] ?? false))
                ->required((bool) ($field['_required'] ?? false))
                ->columnSpan((string) ($field['column_span'] ?? 'full'));
        }

        $schema[] = Textarea::make('workflow_note')
            ->label('Workflow Note')
            ->placeholder('Catatan aksi ini (opsional).');

        return $schema;
    }

    protected function getAvailableActionNames(WorkflowInstance $record): array
    {
        $configured = collect($record->currentStep?->action_schema ?? [])
            ->pluck('name')
            ->filter()
            ->values()
            ->all();

        $transitionActions = collect(data_get($record->workflow_snapshot, 'transitions', []))
            ->where('from_step_id', $record->current_step_id)
            ->pluck('action_name')
            ->all();

        return collect(array_merge($configured, $transitionActions))
            ->push('cancel')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function resolveActionColor(WorkflowInstance $record, string $actionName): string
    {
        $configured = collect($record->currentStep?->action_schema ?? [])
            ->firstWhere('name', $actionName);

        $style = collect((array) ($configured['style'] ?? []));

        if ($style->contains('danger') || in_array($actionName, ['reject', 'cancel'], true)) {
            return 'danger';
        }

        if ($style->contains('warning')) {
            return 'warning';
        }

        if ($style->contains('success') || $actionName === 'approve') {
            return 'success';
        }

        return 'primary';
    }

    protected function hasPendingAssignment(WorkflowInstance $record): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        return $record->assignments
            ->where('status', 'pending')
            ->where('assigned_to_type', 'user')
            ->where('assigned_to_id', $user->getKey())
            ->isNotEmpty();
    }
}
