<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WorkflowDelegationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_id')
                    ->label(FilamentUi::field('tenant_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('from_user_id')
                    ->label(FilamentUi::field('from_user_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('to_user_id')
                    ->label(FilamentUi::field('to_user_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('valid_from')
                    ->label(FilamentUi::field('valid_from'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('valid_until')
                    ->label(FilamentUi::field('valid_until'))
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
