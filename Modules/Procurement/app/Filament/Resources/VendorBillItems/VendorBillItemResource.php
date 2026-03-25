<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\VendorBillItems\Pages\CreateVendorBillItem;
use Modules\Procurement\Filament\Resources\VendorBillItems\Pages\EditVendorBillItem;
use Modules\Procurement\Filament\Resources\VendorBillItems\Pages\ListVendorBillItems;
use Modules\Procurement\Filament\Resources\VendorBillItems\Pages\ViewVendorBillItem;
use Modules\Procurement\Filament\Resources\VendorBillItems\Schemas\VendorBillItemForm;
use Modules\Procurement\Filament\Resources\VendorBillItems\Schemas\VendorBillItemInfolist;
use Modules\Procurement\Filament\Resources\VendorBillItems\Tables\VendorBillItemsTable;
use Modules\Procurement\Models\VendorBillItem;

class VendorBillItemResource extends LocalizedResource
{
    protected static ?string $model = VendorBillItem::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VendorBillItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorBillItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorBillItemsTable::configure($table);
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
            'index' => ListVendorBillItems::route('/'),
            'create' => CreateVendorBillItem::route('/create'),
            'view' => ViewVendorBillItem::route('/{record}'),
            'edit' => EditVendorBillItem::route('/{record}/edit'),
        ];
    }
}
