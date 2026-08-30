<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockAdjustmentLineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('stockAdjustment.id')
                    ->label(FilamentUi::text('Stock adjustment')),
                TextEntry::make('stockItem.name')
                    ->label(FilamentUi::text('Stock item')),
                TextEntry::make('quantity_delta')
                    ->numeric(),
                TextEntry::make('unit_cost')
                    ->money(),
                TextEntry::make('line_total')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
