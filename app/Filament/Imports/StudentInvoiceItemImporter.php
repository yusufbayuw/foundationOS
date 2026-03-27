<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\StudentInvoiceItem;

class StudentInvoiceItemImporter extends BaseModelImporter
{
    protected static ?string $model = StudentInvoiceItem::class;
}
