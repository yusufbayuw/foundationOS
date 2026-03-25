<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\StudentInvoiceItemResource;

class ViewStudentInvoiceItem extends ViewRecord
{
    protected static string $resource = StudentInvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
