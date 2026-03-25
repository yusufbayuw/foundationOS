<?php

namespace Modules\Core\Filament\Resources\Departments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Departments\DepartmentResource;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}
