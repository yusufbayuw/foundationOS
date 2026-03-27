<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class RfqItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('procurementItem.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('procurementItem.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_of_measure'))
                    ->searchable(),
                TextColumn::make('estimated_budget')
                    ->label(\Modules\Core\Support\FilamentUi::field('estimated_budget'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
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
                ...ImportTableActions::make(\App\Filament\Imports\RfqItemImporter::class),
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
