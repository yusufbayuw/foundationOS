<?php

namespace Modules\Sales\Filament\Resources\Customers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Sales\Filament\Resources\Customers\Pages\CreateCustomer;
use Modules\Sales\Filament\Resources\Customers\Pages\EditCustomer;
use Modules\Sales\Filament\Resources\Customers\Pages\ListCustomers;
use Modules\Sales\Filament\Resources\Customers\Pages\ViewCustomer;
use Modules\Sales\Filament\Resources\Customers\Schemas\CustomerForm;
use Modules\Sales\Filament\Resources\Customers\Schemas\CustomerInfolist;
use Modules\Sales\Filament\Resources\Customers\Tables\CustomersTable;
use Modules\Sales\Models\Customer;

class CustomerResource extends ModuleResource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'view' => ViewCustomer::route('/{record}'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
