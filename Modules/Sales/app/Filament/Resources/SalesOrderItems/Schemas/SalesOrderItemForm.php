<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SalesOrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextInput::make('sales_order_id')
                        ->label(FilamentUi::field('sales_order_id'))
                        ->numeric(),
                    TextInput::make('stock_item_id')
                        ->label(FilamentUi::field('stock_item_id'))
                        ->numeric(),
                    Textarea::make('description')
                        ->label(FilamentUi::field('description'))
                        ->columnSpanFull(),
                    TextInput::make('quantity')
                        ->label(FilamentUi::field('quantity'))
                        ->numeric(),
                    TextInput::make('unit_price')
                        ->label(FilamentUi::field('unit_price'))
                        ->numeric(),
                    TextInput::make('line_total')
                        ->label(FilamentUi::field('line_total'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }
}
