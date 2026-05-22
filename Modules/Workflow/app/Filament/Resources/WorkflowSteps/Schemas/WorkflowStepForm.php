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
                            'radio' => 'Radio',
                            'checkbox' => 'Checkbox',
                            'file' => 'File',
                        ])
                        ->required(),
                    Toggle::make('required')->label(FilamentUi::field('required'))->default(false),
                    TagsInput::make('validation')->label(FilamentUi::field('validation')),
                    TagsInput::make('accepted_types')->label(FilamentUi::text('Accepted types'))->placeholder('pdf,jpg,png'),
                    TextInput::make('max_size_kb')->label(FilamentUi::text('Max size').' (KB)')->numeric()->nullable(),
                    KeyValue::make('options')->label(FilamentUi::field('options'))->nullable(),
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
}
