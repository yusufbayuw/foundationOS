<?php

namespace Modules\Employee\Filament\Resources\Employees\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\Employees\EmployeeResource;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
