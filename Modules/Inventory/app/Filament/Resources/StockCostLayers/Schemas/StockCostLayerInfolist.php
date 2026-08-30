<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockCostLayerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('warehouse.name')
                    ->label(FilamentUi::text('Warehouse')),
                TextEntry::make('stockItem.name')
                    ->label(FilamentUi::text('Stock item')),
                TextEntry::make('stockMove.id')
                    ->label(FilamentUi::text('Stock move')),
                TextEntry::make('quantity_remaining')
                    ->numeric(),
                TextEntry::make('unit_cost')
                    ->money(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
