<?php

namespace Modules\Workflow\Filament\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowInstancesRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowInstances';

    protected static ?string $title = 'Workflow Instances';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withTableRelations())
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->label(FilamentUi::text('Workflow'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('workflow_version')
                    ->label(FilamentUi::text('Workflow Version'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('currentStep.name')
                    ->label(FilamentUi::text('Current Step'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label(FilamentUi::text('Status'))
                    ->badge(),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label(FilamentUi::text('Requester'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('started_at')
                    ->label(FilamentUi::text('Started At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_at')
                    ->label(FilamentUi::text('Due At'))
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (WorkflowInstance $record): string => WorkflowInstanceResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
