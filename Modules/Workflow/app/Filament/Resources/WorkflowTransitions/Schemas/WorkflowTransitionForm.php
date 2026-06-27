<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Schemas;

use App\Support\TypedValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Enums\WorkflowRuleType;

class WorkflowTransitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('from_step_id')
                ->label(FilamentUi::text('From step'))
                ->relationship('fromStep', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('to_step_id')
                ->label(FilamentUi::text('To step'))
                ->relationship('toStep', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('action_name')->label(FilamentUi::text('Action name'))->required()->maxLength(255),
            Select::make('rule_type')
                ->label(FilamentUi::field('rule_type'))
                ->options(collect(WorkflowRuleType::cases())->mapWithKeys(fn (WorkflowRuleType $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowRuleType::JsonLogic->value)
                ->required(),
            Textarea::make('condition_rules')
                ->label(FilamentUi::field('condition_rules'))
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                ->dehydrateStateUsing(fn ($state) => blank($state) ? null : json_decode(TypedValue::string($state), true))
                ->helperText(FilamentUi::text('Use valid JsonLogic format'))
                ->columnSpanFull(),
            TextInput::make('priority')->label(FilamentUi::field('priority'))->numeric()->default(0)->required(),
            Toggle::make('is_default')->label(FilamentUi::field('is_default'))->default(false),
            Textarea::make('transition_meta')
                ->label(FilamentUi::field('transition_meta'))
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                ->dehydrateStateUsing(fn ($state) => blank($state) ? null : json_decode(TypedValue::string($state), true))
                ->columnSpanFull(),
        ]);
    }
}
