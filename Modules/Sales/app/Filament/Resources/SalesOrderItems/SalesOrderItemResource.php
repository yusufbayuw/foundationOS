<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Sales\Filament\Resources\SalesOrderItems\Pages\CreateSalesOrderItem;
use Modules\Sales\Filament\Resources\SalesOrderItems\Pages\EditSalesOrderItem;
use Modules\Sales\Filament\Resources\SalesOrderItems\Pages\ListSalesOrderItems;
use Modules\Sales\Filament\Resources\SalesOrderItems\Pages\ViewSalesOrderItem;
use Modules\Sales\Filament\Resources\SalesOrderItems\Schemas\SalesOrderItemForm;
use Modules\Sales\Filament\Resources\SalesOrderItems\Schemas\SalesOrderItemInfolist;
use Modules\Sales\Filament\Resources\SalesOrderItems\Tables\SalesOrderItemsTable;
use Modules\Sales\Models\SalesOrderItem;

class SalesOrderItemResource extends ModuleResource
{
    protected static ?string $model = SalesOrderItem::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return SalesOrderItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesOrderItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesOrderItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesOrderItems::route('/'),
            'create' => CreateSalesOrderItem::route('/create'),
            'view' => ViewSalesOrderItem::route('/{record}'),
            'edit' => EditSalesOrderItem::route('/{record}/edit'),
        ];
    }
}
