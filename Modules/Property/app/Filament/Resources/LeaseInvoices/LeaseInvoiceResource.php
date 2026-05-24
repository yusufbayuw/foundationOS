<?php

namespace Modules\Property\Filament\Resources\LeaseInvoices;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\LeaseInvoices\Pages\CreateLeaseInvoice;
use Modules\Property\Filament\Resources\LeaseInvoices\Pages\EditLeaseInvoice;
use Modules\Property\Filament\Resources\LeaseInvoices\Pages\ListLeaseInvoices;
use Modules\Property\Filament\Resources\LeaseInvoices\Pages\ViewLeaseInvoice;
use Modules\Property\Filament\Resources\LeaseInvoices\Schemas\LeaseInvoiceForm;
use Modules\Property\Filament\Resources\LeaseInvoices\Schemas\LeaseInvoiceInfolist;
use Modules\Property\Filament\Resources\LeaseInvoices\Tables\LeaseInvoicesTable;
use Modules\Property\Models\LeaseInvoice;

class LeaseInvoiceResource extends ModuleResource
{
    protected static ?string $model = LeaseInvoice::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LeaseInvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaseInvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaseInvoicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaseInvoices::route('/'),
            'create' => CreateLeaseInvoice::route('/create'),
            'view' => ViewLeaseInvoice::route('/{record}'),
            'edit' => EditLeaseInvoice::route('/{record}/edit'),
        ];
    }
}
