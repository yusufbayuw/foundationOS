<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages\CreatePurchaseRequisitionItem;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages\EditPurchaseRequisitionItem;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages\ListPurchaseRequisitionItems;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages\ViewPurchaseRequisitionItem;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas\PurchaseRequisitionItemForm;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Schemas\PurchaseRequisitionItemInfolist;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Tables\PurchaseRequisitionItemsTable;
use Modules\Procurement\Models\PurchaseRequisitionItem;

class PurchaseRequisitionItemResource extends LocalizedResource
{
    protected static ?string $model = PurchaseRequisitionItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PurchaseRequisitionItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseRequisitionItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseRequisitionItemsTable::configure($table);
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
            'index' => ListPurchaseRequisitionItems::route('/'),
            'create' => CreatePurchaseRequisitionItem::route('/create'),
            'view' => ViewPurchaseRequisitionItem::route('/{record}'),
            'edit' => EditPurchaseRequisitionItem::route('/{record}/edit'),
        ];
    }
}
