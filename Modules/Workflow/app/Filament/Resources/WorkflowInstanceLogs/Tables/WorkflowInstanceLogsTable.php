<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowInstanceLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow_instance_id')
                    ->label(FilamentUi::field('workflow_instance_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('step_id')
                    ->label(FilamentUi::field('step_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('transition_id')
                    ->label(FilamentUi::field('transition_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('actor_id')
                    ->label(FilamentUi::field('actor_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('log_type')
                    ->label(FilamentUi::field('log_type'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
