<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SalesOrderItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('sales_order_id')
                        ->label(FilamentUi::field('sales_order_id'))
                        ->placeholder('-'),
                    TextEntry::make('stock_item_id')
                        ->label(FilamentUi::field('stock_item_id'))
                        ->placeholder('-'),
                    TextEntry::make('description')
                        ->label(FilamentUi::field('description'))
                        ->placeholder('-'),
                    TextEntry::make('quantity')
                        ->label(FilamentUi::field('quantity'))
                        ->placeholder('-'),
                    TextEntry::make('unit_price')
                        ->label(FilamentUi::field('unit_price'))
                        ->placeholder('-'),
                    TextEntry::make('line_total')
                        ->label(FilamentUi::field('line_total'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
