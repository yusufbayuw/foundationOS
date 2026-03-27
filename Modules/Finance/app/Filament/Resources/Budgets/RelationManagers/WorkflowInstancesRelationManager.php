<?php

namespace Modules\Finance\Filament\Resources\Budgets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WorkflowInstancesRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowInstances';

    protected static ?string $title = 'Workflow Instances';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workflow_version')
                    ->label('Workflow Version')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currentStep.name')
                    ->label('Current Step')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requester')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Started At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record): string => \Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
