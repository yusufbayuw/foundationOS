<?php

namespace Modules\Employee\Filament\Resources\EmployeeDocuments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\EmployeeDocuments\EmployeeDocumentResource;

class CreateEmployeeDocument extends CreateRecord
{
    protected static string $resource = EmployeeDocumentResource::class;
}
