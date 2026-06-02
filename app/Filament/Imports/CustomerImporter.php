<?php

namespace App\Filament\Imports;

use Modules\Sales\Models\Customer;

class CustomerImporter extends BaseModelImporter
{
    protected static ?string $model = Customer::class;
}
