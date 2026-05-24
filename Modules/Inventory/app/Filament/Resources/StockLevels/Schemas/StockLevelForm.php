<?php

namespace Modules\Inventory\Filament\Resources\StockLevels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required(),
                Select::make('stock_item_id')
                    ->relationship('stockItem', 'name')
                    ->required(),
                TextInput::make('quantity_on_hand')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('quantity_reserved')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('average_unit_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
            ]);
    }
}
