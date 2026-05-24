<?php

namespace Modules\Employee\Filament\Resources\EmployeeDocuments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\EmployeeDocuments\EmployeeDocumentResource;

class ViewEmployeeDocument extends ViewRecord
{
    protected static string $resource = EmployeeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
