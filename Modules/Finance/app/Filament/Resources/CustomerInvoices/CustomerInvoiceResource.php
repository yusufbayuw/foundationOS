<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Finance\Filament\Resources\CustomerInvoices\Pages\CreateCustomerInvoice;
use Modules\Finance\Filament\Resources\CustomerInvoices\Pages\EditCustomerInvoice;
use Modules\Finance\Filament\Resources\CustomerInvoices\Pages\ListCustomerInvoices;
use Modules\Finance\Filament\Resources\CustomerInvoices\Pages\ViewCustomerInvoice;
use Modules\Finance\Filament\Resources\CustomerInvoices\Schemas\CustomerInvoiceForm;
use Modules\Finance\Filament\Resources\CustomerInvoices\Schemas\CustomerInvoiceInfolist;
use Modules\Finance\Filament\Resources\CustomerInvoices\Tables\CustomerInvoicesTable;
use Modules\Finance\Models\CustomerInvoice;

class CustomerInvoiceResource extends LocalizedResource
{
    protected static ?string $model = CustomerInvoice::class;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function form(Schema $schema): Schema
    {
        return CustomerInvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerInvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerInvoicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerInvoices::route('/'),
            'create' => CreateCustomerInvoice::route('/create'),
            'view' => ViewCustomerInvoice::route('/{record}'),
            'edit' => EditCustomerInvoice::route('/{record}/edit'),
        ];
    }
}
