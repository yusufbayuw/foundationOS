<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages\CreateStockAdjustmentLine;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages\EditStockAdjustmentLine;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages\ListStockAdjustmentLines;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages\ViewStockAdjustmentLine;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Schemas\StockAdjustmentLineForm;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Schemas\StockAdjustmentLineInfolist;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\Tables\StockAdjustmentLinesTable;
use Modules\Inventory\Models\StockAdjustmentLine;

class StockAdjustmentLineResource extends ModuleResource
{
    protected static ?string $model = StockAdjustmentLine::class;

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentLineForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockAdjustmentLineInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentLinesTable::configure($table);
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
            'index' => ListStockAdjustmentLines::route('/'),
            'create' => CreateStockAdjustmentLine::route('/create'),
            'view' => ViewStockAdjustmentLine::route('/{record}'),
            'edit' => EditStockAdjustmentLine::route('/{record}/edit'),
        ];
    }
}
