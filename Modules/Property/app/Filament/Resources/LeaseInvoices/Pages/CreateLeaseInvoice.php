<?php

namespace Modules\Property\Filament\Resources\LeaseInvoices\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Property\Filament\Resources\LeaseInvoices\LeaseInvoiceResource;

class CreateLeaseInvoice extends CreateRecord
{
    protected static string $resource = LeaseInvoiceResource::class;
}
