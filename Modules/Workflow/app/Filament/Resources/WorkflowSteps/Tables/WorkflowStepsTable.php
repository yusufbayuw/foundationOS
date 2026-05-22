<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label(FilamentUi::field('sort_order'))->sortable(),
            TextColumn::make('name')->label(FilamentUi::field('name'))->searchable(),
            TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
            TextColumn::make('step_type')->label(FilamentUi::field('step_type'))->badge(),
            TextColumn::make('assignee_type')->label(FilamentUi::field('assignee_type'))->badge()->placeholder('-'),
            TextColumn::make('sla_hours')->label(FilamentUi::field('sla_hours'))->numeric()->placeholder('-'),
            IconColumn::make('is_initial')->label(FilamentUi::field('is_initial'))->boolean(),
            IconColumn::make('is_terminal')->label(FilamentUi::field('is_terminal'))->boolean(),
        ]);
    }
}
