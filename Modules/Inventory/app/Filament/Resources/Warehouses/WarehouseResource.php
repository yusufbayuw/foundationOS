<?php

namespace Modules\Inventory\Filament\Resources\Warehouses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\Warehouses\Pages\CreateWarehouse;
use Modules\Inventory\Filament\Resources\Warehouses\Pages\EditWarehouse;
use Modules\Inventory\Filament\Resources\Warehouses\Pages\ListWarehouses;
use Modules\Inventory\Filament\Resources\Warehouses\Pages\ViewWarehouse;
use Modules\Inventory\Filament\Resources\Warehouses\Schemas\WarehouseForm;
use Modules\Inventory\Filament\Resources\Warehouses\Schemas\WarehouseInfolist;
use Modules\Inventory\Filament\Resources\Warehouses\Tables\WarehousesTable;
use Modules\Inventory\Models\Warehouse;
use Modules\Monitoring\Filament\RelationManagers\AuditLogsRelationManager;

class WarehouseResource extends ModuleResource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarehouseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table);
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
            'index' => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'view' => ViewWarehouse::route('/{record}'),
            'edit' => EditWarehouse::route('/{record}/edit'),
        ];
    }
}
