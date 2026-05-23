<?php

namespace Modules\Inventory\Filament\Resources\StockMoves\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockMoveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TextEntry::make('move_number')->label(FilamentUi::field('move_number')),
                    TextEntry::make('move_type')->label(FilamentUi::field('move_type')),
                    TextEntry::make('status')->label(FilamentUi::field('status')),
                    TextEntry::make('warehouse.name')->label(FilamentUi::field('warehouse_id')),
                    TextEntry::make('stockItem.name')->label(FilamentUi::field('stock_item_id')),
                    TextEntry::make('quantity')->label(FilamentUi::field('quantity')),
                    TextEntry::make('unit_cost')->label(FilamentUi::field('unit_cost')),
                    TextEntry::make('total_cost')->label(FilamentUi::field('total_cost')),
                    TextEntry::make('moved_at')->label(FilamentUi::field('moved_at')),
                ]),
        ]);
    }
}
