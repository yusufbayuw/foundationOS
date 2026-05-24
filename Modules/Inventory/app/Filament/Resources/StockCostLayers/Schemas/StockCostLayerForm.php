<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockCostLayerForm
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
                Select::make('stock_move_id')
                    ->relationship('stockMove', 'id')
                    ->required(),
                TextInput::make('quantity_remaining')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
