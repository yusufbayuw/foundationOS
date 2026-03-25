<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\StudentInvoiceItems\StudentInvoiceItemResource;

class EditStudentInvoiceItem extends EditRecord
{
    protected static string $resource = StudentInvoiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
