<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\CustomerInvoices\CustomerInvoiceResource;

class CreateCustomerInvoice extends CreateRecord
{
    protected static string $resource = CustomerInvoiceResource::class;
}
