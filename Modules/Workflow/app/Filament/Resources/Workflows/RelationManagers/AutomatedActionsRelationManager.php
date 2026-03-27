<?php

namespace Modules\Workflow\Filament\Resources\Workflows\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Enums\WorkflowAutomationTrigger;

class AutomatedActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'automatedActions';

    protected static ?string $title = 'Automated Actions';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('step_id')
                ->label('Step')
                ->relationship('step', 'name')
                ->nullable(),
            Select::make('trigger_event')
                ->label('Trigger Event')
                ->options(collect(WorkflowAutomationTrigger::cases())->mapWithKeys(
                    fn (WorkflowAutomationTrigger $case) => [$case->value => str($case->value)->headline()]
                )->all())
                ->required(),
            Select::make('action_type')
                ->label('Action Type')
                ->options(collect(WorkflowAutomationActionType::cases())->mapWithKeys(
                    fn (WorkflowAutomationActionType $case) => [$case->value => str($case->value)->headline()]
                )->all())
                ->required(),
            TextInput::make('name')
                ->label('Name')
                ->maxLength(255),
            KeyValue::make('config')
                ->label('Config')
                ->columnSpanFull(),
            TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label('Is Active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Name')->placeholder('-')->searchable(),
            TextColumn::make('step.name')->label('Step')->placeholder('Workflow Level'),
            TextColumn::make('trigger_event')->label('Trigger Event')->badge(),
            TextColumn::make('action_type')->label('Action Type')->badge(),
            TextColumn::make('sort_order')->label('Sort Order')->sortable(),
            IconColumn::make('is_active')->label('Is Active')->boolean(),
        ]);
    }
}
