<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\CustomerInvoices\CustomerInvoiceResource;

class ViewCustomerInvoice extends ViewRecord
{
    protected static string $resource = CustomerInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
