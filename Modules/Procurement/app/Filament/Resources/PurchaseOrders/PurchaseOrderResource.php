<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrders;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Concerns\ConfiguresGlobalSearch;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
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
    use ConfiguresGlobalSearch;

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'po_number';

    protected static function globalSearchAttributes(): array
    {
        return ['po_number'];
    }

    protected static function globalSearchResultDetails(Model $record): array
    {
        return static::detailStatus($record->status);
    }

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
