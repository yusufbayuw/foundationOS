<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StockAdjustmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TextEntry::make('adjustment_number')->label(FilamentUi::field('adjustment_number')),
                    TextEntry::make('warehouse.name')->label(FilamentUi::field('warehouse_id')),
                    TextEntry::make('reason')->label(FilamentUi::field('reason')),
                    TextEntry::make('status')->label(FilamentUi::field('status')),
                    TextEntry::make('total_value_impact')->label(FilamentUi::field('total_value_impact')),
                ]),
        ]);
    }
}
