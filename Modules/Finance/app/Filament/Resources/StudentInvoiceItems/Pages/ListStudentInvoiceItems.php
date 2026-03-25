<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\StudentInvoiceItemResource;

class ListStudentInvoiceItems extends ListRecords
{
    protected static string $resource = StudentInvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
