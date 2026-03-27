<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\Payment;

class PaymentImporter extends BaseModelImporter
{
    protected static ?string $model = Payment::class;
}
