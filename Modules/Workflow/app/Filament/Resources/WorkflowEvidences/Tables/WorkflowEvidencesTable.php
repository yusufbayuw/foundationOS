<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowEvidencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow_instance_id')
                    ->label(FilamentUi::field('workflow_instance_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('workflow_step_id')
                    ->label(FilamentUi::field('workflow_step_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('uploaded_by')
                    ->label(FilamentUi::field('uploaded_by'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('file_path')
                    ->label(FilamentUi::field('file_path'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('original_filename')
                    ->label(FilamentUi::field('original_filename'))
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
