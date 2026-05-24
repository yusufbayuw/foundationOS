<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\CustomerInvoiceItemResource;

class ListCustomerInvoiceItems extends ListRecords
{
    protected static string $resource = CustomerInvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
