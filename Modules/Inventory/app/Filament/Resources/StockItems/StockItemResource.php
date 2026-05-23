<?php

namespace Modules\Inventory\Filament\Resources\StockItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockItems\Pages\CreateStockItem;
use Modules\Inventory\Filament\Resources\StockItems\Pages\EditStockItem;
use Modules\Inventory\Filament\Resources\StockItems\Pages\ListStockItems;
use Modules\Inventory\Filament\Resources\StockItems\Pages\ViewStockItem;
use Modules\Inventory\Filament\Resources\StockItems\Schemas\StockItemForm;
use Modules\Inventory\Filament\Resources\StockItems\Schemas\StockItemInfolist;
use Modules\Inventory\Filament\Resources\StockItems\Tables\StockItemsTable;
use Modules\Inventory\Models\StockItem;
use Modules\Monitoring\Filament\RelationManagers\AuditLogsRelationManager;

class StockItemResource extends ModuleResource
{
    protected static ?string $model = StockItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AuditLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockItems::route('/'),
            'create' => CreateStockItem::route('/create'),
            'view' => ViewStockItem::route('/{record}'),
            'edit' => EditStockItem::route('/{record}/edit'),
        ];
    }
}
