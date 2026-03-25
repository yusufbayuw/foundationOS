<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\StudentInvoices\StudentInvoiceResource;

class ViewStudentInvoice extends ViewRecord
{
    protected static string $resource = StudentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
