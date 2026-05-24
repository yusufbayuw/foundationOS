<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class SalesOrderItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sales_order_id')
                    ->label(FilamentUi::field('sales_order_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_item_id')
                    ->label(FilamentUi::field('stock_item_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label(FilamentUi::field('description'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(FilamentUi::field('quantity'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
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
