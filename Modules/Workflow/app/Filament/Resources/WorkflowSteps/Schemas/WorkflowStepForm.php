<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Enums\WorkflowStepType;

class WorkflowStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label(FilamentUi::field('code'))->required()->maxLength(255),
            TextInput::make('name')->label(FilamentUi::field('name'))->required()->maxLength(255),
            Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
            Select::make('step_type')
                ->label(FilamentUi::field('step_type'))
                ->options(collect(WorkflowStepType::cases())->mapWithKeys(fn (WorkflowStepType $case) => [$case->value => str($case->value)->headline()])->all())
                ->required(),
            Select::make('assignee_type')
                ->label(FilamentUi::field('assignee_type'))
                ->options(collect(WorkflowAssigneeType::cases())->mapWithKeys(fn (WorkflowAssigneeType $case) => [$case->value => str($case->value)->headline()])->all())
                ->nullable(),
            TextInput::make('assignee_value')->label(FilamentUi::field('assignee_value'))->maxLength(255),
            KeyValue::make('assignee_config')
                ->label(FilamentUi::field('assignee_config'))
                ->nullable(),
            TextInput::make('sla_hours')->label(FilamentUi::field('sla_hours'))->numeric()->nullable(),
            TextInput::make('sort_order')->label(FilamentUi::field('sort_order'))->numeric()->default(0)->required(),
            Toggle::make('is_initial')->label(FilamentUi::field('is_initial'))->default(false),
            Toggle::make('is_terminal')->label(FilamentUi::field('is_terminal'))->default(false),
            Toggle::make('allow_reassign')->label(FilamentUi::field('allow_reassign'))->default(false),
            Toggle::make('allow_delegate')->label(FilamentUi::field('allow_delegate'))->default(false),
            Repeater::make('form_schema')
                ->label(FilamentUi::field('form_schema'))
                ->schema([
                    TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                    TextInput::make('label')->label(FilamentUi::field('label'))->required(),
                    Select::make('type')
                        ->label(FilamentUi::field('type'))
                        ->options([
                            'text' => 'Text',
                            'textarea' => 'Textarea',
                            'number' => 'Number',
                            'date' => 'Date',
                            'datetime' => 'Datetime',
                            'select' => 'Select',
                            'multiselect' => 'Multi-select',
                            'radio' => 'Radio',
                            'checkbox' => 'Checkbox',
                            'file' => 'File',
                        ])
                        ->required(),
                    Toggle::make('required')->label(FilamentUi::field('required'))->default(false),
                    TagsInput::make('validation')->label(FilamentUi::field('validation')),
                    TagsInput::make('accepted_types')->label(FilamentUi::text('Accepted types'))->placeholder('pdf,jpg,png'),
                    TextInput::make('max_size_kb')->label(FilamentUi::text('Max size').' (KB)')->numeric()->nullable(),
                    KeyValue::make('options')
                        ->label(FilamentUi::field('options'))
                        ->helperText(FilamentUi::text('Static value and label pairs.'))
                        ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'multiselect', 'radio'], true))
                        ->nullable(),
                    Group::make()
                        ->schema([
                            Select::make('kind')
                                ->label(FilamentUi::text('Dynamic option source'))
                                ->options([
                                    'static' => FilamentUi::text('Static'),
                                    'eloquent' => 'Eloquent',
                                    'enum' => 'Enum',
                                ])
                                ->live()
                                ->nullable(),
                            KeyValue::make('options')
                                ->label(FilamentUi::field('options'))
                                ->visible(fn (Get $get): bool => $get('kind') === 'static')
                                ->nullable(),
                            Select::make('model')
                                ->label(FilamentUi::text('Whitelisted model'))
                                ->options(self::dynamicSourceModelOptions())
                                ->searchable()
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent')
                                ->required(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            TextInput::make('scope')
                                ->label(FilamentUi::text('Model scope'))
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            TextInput::make('label')
                                ->label(FilamentUi::text('Label column'))
                                ->default('name')
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            TextInput::make('value')
                                ->label(FilamentUi::text('Value column'))
                                ->default('id')
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            TextInput::make('search_field')
                                ->label(FilamentUi::text('Search column'))
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            Toggle::make('tenant_aware')
                                ->label(FilamentUi::text('Tenant aware'))
                                ->default(true)
                                ->visible(fn (Get $get): bool => $get('kind') === 'eloquent'),
                            TextInput::make('class')
                                ->label(FilamentUi::text('Enum class'))
                                ->visible(fn (Get $get): bool => $get('kind') === 'enum')
                                ->required(fn (Get $get): bool => $get('kind') === 'enum'),
                        ])
                        ->statePath('options_source')
                        ->columns(2)
                        ->columnSpanFull()
                        ->dehydrated(fn (Get $get): bool => filled($get('kind')))
                        ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'multiselect', 'radio'], true)),
                ])
                ->columnSpanFull(),
            Repeater::make('action_schema')
                ->label(FilamentUi::field('action_schema'))
                ->schema([
                    TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                    TextInput::make('label')->label(FilamentUi::field('label'))->required(),
                    CheckboxList::make('style')
                        ->label(FilamentUi::field('style'))
                        ->options([
                            'primary' => 'Primary',
                            'danger' => 'Danger',
                            'success' => 'Success',
                            'warning' => 'Warning',
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function dynamicSourceModelOptions(): array
    {
        return collect(config('workflow-dynamic-sources.models', []))
            ->mapWithKeys(function (string|array $configuration, string $alias): array {
                $modelClass = is_array($configuration)
                    ? $configuration['class']
                    : $configuration;

                return [$alias => class_basename($modelClass)];
            })
            ->all();
    }
}
