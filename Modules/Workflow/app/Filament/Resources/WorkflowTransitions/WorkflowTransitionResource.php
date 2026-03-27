<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Enums\WorkflowRuleType;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowTransitionResource extends LocalizedResource
{
    protected static ?string $model = WorkflowTransition::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('from_step_id')
                ->label('From Step')
                ->relationship('fromStep', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('to_step_id')
                ->label('To Step')
                ->relationship('toStep', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('action_name')->label('Action Name')->required()->maxLength(255),
            Select::make('rule_type')
                ->label('Rule Type')
                ->options(collect(WorkflowRuleType::cases())->mapWithKeys(fn (WorkflowRuleType $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowRuleType::JsonLogic->value)
                ->required(),
            Textarea::make('condition_rules')
                ->label('Condition Rules')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                ->dehydrateStateUsing(fn ($state) => blank($state) ? null : json_decode((string) $state, true))
                ->helperText('Gunakan format JsonLogic valid.')
                ->columnSpanFull(),
            TextInput::make('priority')->label('Priority')->numeric()->default(0)->required(),
            Toggle::make('is_default')->label('Is Default')->default(false),
            Textarea::make('transition_meta')
                ->label('Transition Meta')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                ->dehydrateStateUsing(fn ($state) => blank($state) ? null : json_decode((string) $state, true))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fromStep.name')->label('From Step')->searchable(),
            TextColumn::make('toStep.name')->label('To Step')->placeholder('Terminal'),
            TextColumn::make('action_name')->label('Action Name')->badge(),
            TextColumn::make('priority')->label('Priority')->numeric()->sortable(),
            IconColumn::make('is_default')->label('Is Default')->boolean(),
        ]);
    }

    public static function isScopedToTenant(): bool
    {
        return false;
    }
}
