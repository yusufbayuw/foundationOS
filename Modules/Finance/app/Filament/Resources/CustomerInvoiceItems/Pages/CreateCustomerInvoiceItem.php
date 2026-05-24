<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoiceItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\CustomerInvoiceItems\CustomerInvoiceItemResource;

class CreateCustomerInvoiceItem extends CreateRecord
{
    protected static string $resource = CustomerInvoiceItemResource::class;
}
