<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowTransitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fromStep.name')->label(FilamentUi::text('From step'))->searchable(),
            TextColumn::make('toStep.name')->label(FilamentUi::text('To step'))->placeholder('-'),
            TextColumn::make('action_name')->label(FilamentUi::text('Action name'))->badge(),
            TextColumn::make('priority')->label(FilamentUi::field('priority'))->numeric()->sortable(),
            IconColumn::make('is_default')->label(FilamentUi::field('is_default'))->boolean(),
        ]);
    }
}
