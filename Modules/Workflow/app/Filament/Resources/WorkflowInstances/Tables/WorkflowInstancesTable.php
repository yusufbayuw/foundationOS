<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowInstancesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('workflow.name')->label(FilamentUi::text('Workflow'))->searchable(),
            TextColumn::make('subject_label')->label(FilamentUi::field('subject_label'))->searchable()->placeholder('-'),
            TextColumn::make('requester.name')->label(FilamentUi::text('Requester'))->searchable(),
            TextColumn::make('currentStep.name')->label(FilamentUi::text('Current step'))->placeholder('-'),
            TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            TextColumn::make('started_at')->label(FilamentUi::field('started_at'))->dateTime()->sortable(),
            TextColumn::make('due_at')->label(FilamentUi::field('due_at'))->dateTime()->sortable()->placeholder('-'),
        ]);
    }
}
