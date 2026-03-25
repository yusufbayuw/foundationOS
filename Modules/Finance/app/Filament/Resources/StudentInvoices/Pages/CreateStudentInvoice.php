<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\StudentInvoices\StudentInvoiceResource;

class CreateStudentInvoice extends CreateRecord
{
    protected static string $resource = StudentInvoiceResource::class;
}
