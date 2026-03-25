<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Pages\ViewPurchaseOrder;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Schemas\PurchaseOrderInfolist;
use Modules\Procurement\Filament\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use Modules\Procurement\Models\PurchaseOrder;

class PurchaseOrderResource extends LocalizedResource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrdersTable::configure($table);
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
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'view' => ViewPurchaseOrder::route('/{record}'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
