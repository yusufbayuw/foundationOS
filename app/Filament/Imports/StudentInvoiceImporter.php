<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\StudentInvoice;

class StudentInvoiceImporter extends BaseModelImporter
{
    protected static ?string $model = StudentInvoice::class;
}
