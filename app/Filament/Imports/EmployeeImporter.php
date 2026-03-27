<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\Employee;

class EmployeeImporter extends BaseModelImporter
{
    protected static ?string $model = Employee::class;
}
