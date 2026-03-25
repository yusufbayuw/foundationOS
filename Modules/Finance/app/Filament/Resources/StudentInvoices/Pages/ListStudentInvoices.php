<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\StudentInvoices\StudentInvoiceResource;

class ListStudentInvoices extends ListRecords
{
    protected static string $resource = StudentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
