<?php

namespace Modules\Sales\Filament\Resources\Customers\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\Customers\CustomerResource;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;
}
