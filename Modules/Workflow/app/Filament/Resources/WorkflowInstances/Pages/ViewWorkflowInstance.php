<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\Budgets\BudgetResource;
use Modules\Finance\Models\Budget;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\DynamicOptionsResolver;
use Modules\Workflow\Support\WorkflowContextData;

class ViewWorkflowInstance extends ViewRecord
{
    protected static string $resource = WorkflowInstanceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var WorkflowInstance $record */
        $record = $this->getRecord()->loadMissing(['currentStep', 'assignments']);
        $step = $record->currentStep;
        $headerActions = [];
        $subjectUrl = $this->resolveSubjectUrl($record);

        if ($subjectUrl) {
            $headerActions[] = Action::make('openSubject')
                ->label(FilamentUi::text('Open Subject'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url($subjectUrl)
                ->openUrlInNewTab();
        }

        if (! $step || ! $this->hasPendingAssignment($record)) {
            return $headerActions;
        }

        $actions = $headerActions;

        if ($this->getReturnTargetOptions($record) !== []) {
            $actions[] = Action::make('returnToStep')
                ->label(FilamentUi::text('Return To Step'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->authorize(fn (): bool => $this->hasPendingAssignment($record))
                ->form([
                    Select::make('target_step_id')
                        ->label(FilamentUi::text('Target Step'))
                        ->options($this->getReturnTargetOptions($record))
                        ->required(),
                    ...$this->buildDynamicFormSchema($record),
                ])
                ->action(function (array $data) use ($record): void {
                    /** @var User $actor */
                    $actor = auth()->user();

                    app(WorkflowEngine::class)->returnToStep(
                        $record,
                        (int) $data['target_step_id'],
                        Arr::except($data, ['target_step_id', 'workflow_note']),
                        $actor,
                        Arr::get($data, 'workflow_note'),
                    );

                    $this->record = $record->fresh();

                    Notification::make()
                        ->title('Workflow returned to the selected step.')
                        ->success()
                        ->send();
                });
        }

        foreach ($this->getAvailableActionNames($record) as $actionName) {
            $actions[] = Action::make($actionName)
                ->label((string) str($actionName)->headline())
                ->color($this->resolveActionColor($record, $actionName))
                ->authorize(fn (): bool => $this->hasPendingAssignment($record))
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

            $type = (string) ($field['type'] ?? 'text');

            $component = match ($type) {
                'textarea' => Textarea::make($name),
                'number' => TextInput::make($name)->numeric(),
                'date' => DatePicker::make($name),
                'datetime' => DateTimePicker::make($name),
                'select' => $this->buildSelectComponent($name, $field, $record),
                'multiselect' => $this->buildSelectComponent($name, $field, $record, multiple: true),
                'radio' => Radio::make($name)->options($this->resolveSelectOptions($field, $record)),
                'checkbox' => Checkbox::make($name),
                'file' => FileUpload::make($name)->disk(config('workflow.default_file_disk')),
                default => TextInput::make($name),
            };

            if (($field['type'] ?? null) === 'file' && ! empty($field['accepted_types']) && is_array($field['accepted_types'])) {
                $component->acceptedFileTypes(array_map(
                    fn (string $type): string => str_contains($type, '/') ? $type : '.'.ltrim($type, '.'),
                    $field['accepted_types'],
                ));
            }

            if (($field['type'] ?? null) === 'file' && ! empty($field['max_size_kb'])) {
                $component->maxSize((int) $field['max_size_kb']);
            }

            $component
                ->label((string) ($field['label'] ?? str($name)->headline()))
                ->helperText($field['help_text'] ?? null)
                ->default($field['default_value'] ?? null)
                ->disabled((bool) ($field['_disabled'] ?? false))
                ->required((bool) ($field['_required'] ?? false))
                ->columnSpan((string) ($field['column_span'] ?? 'full'));

            if (method_exists($component, 'placeholder')) {
                $component->placeholder((string) ($field['placeholder'] ?? ''));
            }

            $schema[] = $component;
        }

        $schema[] = Textarea::make('workflow_note')
            ->label(FilamentUi::text('Workflow Note'))
            ->placeholder(FilamentUi::text('Note for this action (optional).'));

        return $schema;
    }

    protected function buildSelectComponent(
        string $name,
        array $field,
        WorkflowInstance $record,
        bool $multiple = false,
    ): Select {
        $component = Select::make($name)
            ->multiple($multiple)
            ->searchable()
            ->optionsLimit(min(50, (int) config('workflow-dynamic-sources.max_results', 500)));

        $source = $this->resolveOptionsSource($field);

        if (($source['kind'] ?? 'static') !== 'eloquent') {
            return $component->options($this->resolveSelectOptions($field, $record));
        }

        $resolver = app(DynamicOptionsResolver::class);
        $tenantId = $record->tenant_id;

        $component->getSearchResultsUsing(
            fn (string $search): array => $resolver->resolveForSelect(
                array_merge($source, ['search' => $search]),
                $tenantId,
            ),
        );

        if ($multiple) {
            return $component->getOptionLabelsUsing(
                fn (array $values): array => collect($values)
                    ->mapWithKeys(function (mixed $value) use ($resolver, $source, $tenantId): array {
                        $label = $resolver->resolveLabel($source, $value, $tenantId);

                        return $label === null ? [] : [$value => $label];
                    })
                    ->all(),
            );
        }

        return $component->getOptionLabelUsing(
            fn (mixed $value): ?string => $resolver->resolveLabel($source, $value, $tenantId),
        );
    }

    /**
     * @return array<int|string, string>
     */
    protected function resolveSelectOptions(array $field, WorkflowInstance $record): array
    {
        return app(DynamicOptionsResolver::class)->resolveForSelect(
            $this->resolveOptionsSource($field),
            $record->tenant_id,
        );
    }

    protected function resolveOptionsSource(array $field): array
    {
        if (is_array($field['options_source'] ?? null)) {
            return $field['options_source'];
        }

        return [
            'kind' => 'static',
            'options' => $field['options'] ?? [],
        ];
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

    protected function getReturnTargetOptions(WorkflowInstance $record): array
    {
        return collect(data_get($record->workflow_snapshot, 'steps', []))
            ->filter(fn (array $step): bool => ($step['id'] ?? null) !== $record->current_step_id && ! ($step['is_terminal'] ?? false))
            ->sortBy('sort_order')
            ->mapWithKeys(fn (array $step): array => [(string) $step['id'] => (string) ($step['name'] ?? $step['code'] ?? $step['id'])])
            ->all();
    }

    protected function resolveSubjectUrl(WorkflowInstance $record): ?string
    {
        if (! $record->subject) {
            return null;
        }

        return match ($record->subject_type) {
            PurchaseRequisition::class => PurchaseRequisitionResource::getUrl('view', ['record' => $record->subject]),
            Budget::class => BudgetResource::getUrl('view', ['record' => $record->subject]),
            default => null,
        };
    }
}
