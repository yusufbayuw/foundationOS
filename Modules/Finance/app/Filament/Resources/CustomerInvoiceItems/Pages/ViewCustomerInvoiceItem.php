<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\CustomerInvoiceItemResource;

class ViewCustomerInvoiceItem extends ViewRecord
{
    protected static string $resource = CustomerInvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
