<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoiceItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages\CreateCustomerInvoiceItem;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages\EditCustomerInvoiceItem;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages\ListCustomerInvoiceItems;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages\ViewCustomerInvoiceItem;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Schemas\CustomerInvoiceItemForm;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Schemas\CustomerInvoiceItemInfolist;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\Tables\CustomerInvoiceItemsTable;
use Modules\Finance\Models\CustomerInvoiceItem;

class CustomerInvoiceItemResource extends ModuleResource
{
    protected static ?string $model = CustomerInvoiceItem::class;

    public static function form(Schema $schema): Schema
    {
        return CustomerInvoiceItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerInvoiceItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerInvoiceItemsTable::configure($table);
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
            'index' => ListCustomerInvoiceItems::route('/'),
            'create' => CreateCustomerInvoiceItem::route('/create'),
            'view' => ViewCustomerInvoiceItem::route('/{record}'),
            'edit' => EditCustomerInvoiceItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
