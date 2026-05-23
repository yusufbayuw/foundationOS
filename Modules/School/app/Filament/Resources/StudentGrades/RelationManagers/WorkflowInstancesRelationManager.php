<?php

namespace Modules\School\Filament\Resources\StudentGrades\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;

class WorkflowInstancesRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowInstances';

    protected static ?string $title = 'Workflow Instances';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->label(FilamentUi::text('Workflow'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(FilamentUi::field('status')),
                Tables\Columns\TextColumn::make('currentStep.name')
                    ->label(FilamentUi::text('Current Step'))
                    ->placeholder('-'),
            ])
            ->recordUrl(fn ($record) => WorkflowInstanceResource::getUrl('view', ['record' => $record]));
    }
}
