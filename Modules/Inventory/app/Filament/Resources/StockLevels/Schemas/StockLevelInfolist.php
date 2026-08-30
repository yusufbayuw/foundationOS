<?php

namespace Modules\Inventory\Filament\Resources\StockLevels\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockLevelInfolist
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
                TextEntry::make('quantity_on_hand')
                    ->numeric(),
                TextEntry::make('quantity_reserved')
                    ->numeric(),
                TextEntry::make('average_unit_cost')
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
