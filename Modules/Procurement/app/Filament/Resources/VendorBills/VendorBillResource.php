<?php

namespace Modules\Procurement\Filament\Resources\VendorBills;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\VendorBills\Pages\CreateVendorBill;
use Modules\Procurement\Filament\Resources\VendorBills\Pages\EditVendorBill;
use Modules\Procurement\Filament\Resources\VendorBills\Pages\ListVendorBills;
use Modules\Procurement\Filament\Resources\VendorBills\Pages\ViewVendorBill;
use Modules\Procurement\Filament\Resources\VendorBills\Schemas\VendorBillForm;
use Modules\Procurement\Filament\Resources\VendorBills\Schemas\VendorBillInfolist;
use Modules\Procurement\Filament\Resources\VendorBills\Tables\VendorBillsTable;
use Modules\Procurement\Models\VendorBill;

class VendorBillResource extends LocalizedResource
{
    protected static ?string $model = VendorBill::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VendorBillForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorBillInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorBillsTable::configure($table);
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
            'index' => ListVendorBills::route('/'),
            'create' => CreateVendorBill::route('/create'),
            'view' => ViewVendorBill::route('/{record}'),
            'edit' => EditVendorBill::route('/{record}/edit'),
        ];
    }
}
