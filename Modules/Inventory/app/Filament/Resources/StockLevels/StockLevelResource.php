<?php

namespace Modules\Inventory\Filament\Resources\StockLevels;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockLevels\Pages\CreateStockLevel;
use Modules\Inventory\Filament\Resources\StockLevels\Pages\EditStockLevel;
use Modules\Inventory\Filament\Resources\StockLevels\Pages\ListStockLevels;
use Modules\Inventory\Filament\Resources\StockLevels\Pages\ViewStockLevel;
use Modules\Inventory\Filament\Resources\StockLevels\Schemas\StockLevelForm;
use Modules\Inventory\Filament\Resources\StockLevels\Schemas\StockLevelInfolist;
use Modules\Inventory\Filament\Resources\StockLevels\Tables\StockLevelsTable;
use Modules\Inventory\Models\StockLevel;

class StockLevelResource extends ModuleResource
{
    protected static ?string $model = StockLevel::class;

    public static function form(Schema $schema): Schema
    {
        return StockLevelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockLevelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockLevelsTable::configure($table);
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
            'index' => ListStockLevels::route('/'),
            'create' => CreateStockLevel::route('/create'),
            'view' => ViewStockLevel::route('/{record}'),
            'edit' => EditStockLevel::route('/{record}/edit'),
        ];
    }
}
