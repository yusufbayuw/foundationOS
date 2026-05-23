<?php

namespace Modules\Inventory\Filament\Resources\StockMoves\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class StockMovesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('move_number')->label(FilamentUi::field('move_number'))->searchable(),
                TextColumn::make('move_type')->label(FilamentUi::field('move_type')),
                TextColumn::make('stockItem.name')->label(FilamentUi::field('stock_item_id')),
                TextColumn::make('warehouse.name')->label(FilamentUi::field('warehouse_id')),
                TextColumn::make('quantity')->label(FilamentUi::field('quantity')),
                TextColumn::make('total_cost')->label(FilamentUi::field('total_cost')),
                TextColumn::make('committed_at')->label(FilamentUi::field('committed_at'))->dateTime(),
            ])
            ->recordActions([ViewAction::make()])
            ->defaultSort('id', 'desc');
    }
}
