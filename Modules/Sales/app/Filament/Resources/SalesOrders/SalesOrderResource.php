<?php

namespace Modules\Sales\Filament\Resources\SalesOrders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Sales\Filament\Resources\SalesOrders\Pages\CreateSalesOrder;
use Modules\Sales\Filament\Resources\SalesOrders\Pages\EditSalesOrder;
use Modules\Sales\Filament\Resources\SalesOrders\Pages\ListSalesOrders;
use Modules\Sales\Filament\Resources\SalesOrders\Pages\ViewSalesOrder;
use Modules\Sales\Filament\Resources\SalesOrders\Schemas\SalesOrderForm;
use Modules\Sales\Filament\Resources\SalesOrders\Schemas\SalesOrderInfolist;
use Modules\Sales\Filament\Resources\SalesOrders\Tables\SalesOrdersTable;
use Modules\Sales\Models\SalesOrder;

class SalesOrderResource extends ModuleResource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return SalesOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesOrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesOrders::route('/'),
            'create' => CreateSalesOrder::route('/create'),
            'view' => ViewSalesOrder::route('/{record}'),
            'edit' => EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
