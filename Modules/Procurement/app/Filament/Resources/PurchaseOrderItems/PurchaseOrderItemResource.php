<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages\CreatePurchaseOrderItem;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages\EditPurchaseOrderItem;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages\ListPurchaseOrderItems;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages\ViewPurchaseOrderItem;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas\PurchaseOrderItemForm;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Schemas\PurchaseOrderItemInfolist;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\Tables\PurchaseOrderItemsTable;
use Modules\Procurement\Models\PurchaseOrderItem;

class PurchaseOrderItemResource extends LocalizedResource
{
    protected static ?string $model = PurchaseOrderItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseOrderItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrderItemsTable::configure($table);
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
            'index' => ListPurchaseOrderItems::route('/'),
            'create' => CreatePurchaseOrderItem::route('/create'),
            'view' => ViewPurchaseOrderItem::route('/{record}'),
            'edit' => EditPurchaseOrderItem::route('/{record}/edit'),
        ];
    }
}
