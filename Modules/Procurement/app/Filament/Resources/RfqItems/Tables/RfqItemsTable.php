<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Tables;

use App\Filament\Imports\RfqItemImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class RfqItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('procurementItem.name')
                    ->label(FilamentUi::field('procurementItem.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_budget')
                    ->label(FilamentUi::field('estimated_budget'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(RfqItemImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
