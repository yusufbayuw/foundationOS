<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\StudentInvoiceItemResource;

class CreateStudentInvoiceItem extends CreateRecord
{
    protected static string $resource = StudentInvoiceItemResource::class;
}
