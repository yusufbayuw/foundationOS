<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockCostLayers\Pages\CreateStockCostLayer;
use Modules\Inventory\Filament\Resources\StockCostLayers\Pages\EditStockCostLayer;
use Modules\Inventory\Filament\Resources\StockCostLayers\Pages\ListStockCostLayers;
use Modules\Inventory\Filament\Resources\StockCostLayers\Pages\ViewStockCostLayer;
use Modules\Inventory\Filament\Resources\StockCostLayers\Schemas\StockCostLayerForm;
use Modules\Inventory\Filament\Resources\StockCostLayers\Schemas\StockCostLayerInfolist;
use Modules\Inventory\Filament\Resources\StockCostLayers\Tables\StockCostLayersTable;
use Modules\Inventory\Models\StockCostLayer;

class StockCostLayerResource extends ModuleResource
{
    protected static ?string $model = StockCostLayer::class;

    public static function form(Schema $schema): Schema
    {
        return StockCostLayerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockCostLayerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockCostLayersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockCostLayers::route('/'),
            'create' => CreateStockCostLayer::route('/create'),
            'view' => ViewStockCostLayer::route('/{record}'),
            'edit' => EditStockCostLayer::route('/{record}/edit'),
        ];
    }
}
