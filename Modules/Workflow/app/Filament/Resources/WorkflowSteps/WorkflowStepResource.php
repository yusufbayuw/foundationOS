<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Enums\WorkflowAssigneeType;
use Modules\Workflow\Enums\WorkflowStepType;
use Modules\Workflow\Models\WorkflowStep;

class WorkflowStepResource extends LocalizedResource
{
    protected static ?string $model = WorkflowStep::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label('Code')->required()->maxLength(255),
            TextInput::make('name')->label('Name')->required()->maxLength(255),
            Textarea::make('description')->label('Description')->columnSpanFull(),
            Select::make('step_type')
                ->label('Step Type')
                ->options(collect(WorkflowStepType::cases())->mapWithKeys(fn (WorkflowStepType $case) => [$case->value => str($case->value)->headline()])->all())
                ->required(),
            Select::make('assignee_type')
                ->label('Assignee Type')
                ->options(collect(WorkflowAssigneeType::cases())->mapWithKeys(fn (WorkflowAssigneeType $case) => [$case->value => str($case->value)->headline()])->all())
                ->nullable(),
            TextInput::make('assignee_value')->label('Assignee Value')->maxLength(255),
            KeyValue::make('assignee_config')
                ->label('Assignee Config')
                ->nullable(),
            TextInput::make('sla_hours')->label('SLA Hours')->numeric()->nullable(),
            TextInput::make('sort_order')->label('Sort Order')->numeric()->default(0)->required(),
            Toggle::make('is_initial')->label('Is Initial')->default(false),
            Toggle::make('is_terminal')->label('Is Terminal')->default(false),
            Toggle::make('allow_reassign')->label('Allow Reassign')->default(false),
            Toggle::make('allow_delegate')->label('Allow Delegate')->default(false),
            Repeater::make('form_schema')
                ->label('Form Schema')
                ->schema([
                    TextInput::make('name')->label('Name')->required(),
                    TextInput::make('label')->label('Label')->required(),
                    Select::make('type')
                        ->label('Type')
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
                    Toggle::make('required')->label('Required')->default(false),
                    TagsInput::make('validation')->label('Validation'),
                    TagsInput::make('accepted_types')->label('Accepted Types')->placeholder('pdf,jpg,png'),
                    TextInput::make('max_size_kb')->label('Max Size (KB)')->numeric()->nullable(),
                    KeyValue::make('options')->label('Options')->nullable(),
                ])
                ->columnSpanFull(),
            Repeater::make('action_schema')
                ->label('Action Schema')
                ->schema([
                    TextInput::make('name')->label('Name')->required(),
                    TextInput::make('label')->label('Label')->required(),
                    CheckboxList::make('style')
                        ->label('Style')
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

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('Sort Order')->sortable(),
            TextColumn::make('name')->label('Name')->searchable(),
            TextColumn::make('code')->label('Code')->searchable(),
            TextColumn::make('step_type')->label('Step Type')->badge(),
            TextColumn::make('assignee_type')->label('Assignee Type')->badge()->placeholder('-'),
            TextColumn::make('sla_hours')->label('SLA Hours')->numeric()->placeholder('-'),
            IconColumn::make('is_initial')->label('Is Initial')->boolean(),
            IconColumn::make('is_terminal')->label('Is Terminal')->boolean(),
        ]);
    }

    public static function isScopedToTenant(): bool
    {
        return false;
    }
}
