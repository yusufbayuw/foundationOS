<?php

namespace Modules\Inventory\Filament\Resources\StockItems\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TextEntry::make('code')->label(FilamentUi::field('code')),
                    TextEntry::make('name')->label(FilamentUi::field('name')),
                    TextEntry::make('procurementItem.name')->label(FilamentUi::field('procurement_item_id')),
                    TextEntry::make('valuation_method')->label(FilamentUi::field('valuation_method')),
                    TextEntry::make('unit_of_measure')->label(FilamentUi::field('unit_of_measure')),
                    IconEntry::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
                ]),
        ]);
    }
}
