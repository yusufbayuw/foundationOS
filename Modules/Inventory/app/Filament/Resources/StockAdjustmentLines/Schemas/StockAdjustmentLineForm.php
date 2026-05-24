<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockAdjustmentLineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('stock_adjustment_id')
                    ->relationship('stockAdjustment', 'id')
                    ->required(),
                Select::make('stock_item_id')
                    ->relationship('stockItem', 'name')
                    ->required(),
                TextInput::make('quantity_delta')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('line_total')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
