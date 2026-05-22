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
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Enums\WorkflowAutomationTrigger;

class AutomatedActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'automatedActions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Automated actions');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(static::formComponents());
    }

    public static function formComponents(): array
    {
        return [
            Select::make('step_id')
                ->label(FilamentUi::field('step_id'))
                ->relationship('step', 'name')
                ->nullable(),
            Select::make('trigger_event')
                ->label(FilamentUi::field('trigger_event'))
                ->options(collect(WorkflowAutomationTrigger::cases())->mapWithKeys(
                    fn (WorkflowAutomationTrigger $case) => [$case->value => str($case->value)->headline()]
                )->all())
                ->required(),
            Select::make('action_type')
                ->label(FilamentUi::field('action_type'))
                ->options(collect(WorkflowAutomationActionType::cases())->mapWithKeys(
                    fn (WorkflowAutomationActionType $case) => [$case->value => str($case->value)->headline()]
                )->all())
                ->required(),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->maxLength(255),
            KeyValue::make('config')
                ->label(FilamentUi::field('config'))
                ->columnSpanFull(),
            TextInput::make('sort_order')
                ->label(FilamentUi::field('sort_order'))
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label(FilamentUi::field('is_active'))
                ->default(true),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->columns(static::tableColumns());
    }

    public static function tableColumns(): array
    {
        return [
            TextColumn::make('name')->label(FilamentUi::field('name'))->placeholder('-')->searchable(),
            TextColumn::make('step.name')->label(FilamentUi::field('step_id'))->placeholder('-'),
            TextColumn::make('trigger_event')->label(FilamentUi::field('trigger_event'))->badge(),
            TextColumn::make('action_type')->label(FilamentUi::field('action_type'))->badge(),
            TextColumn::make('sort_order')->label(FilamentUi::field('sort_order'))->sortable(),
            IconColumn::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
        ];
    }
}
