<?php

namespace App\Filament\Imports;

use Modules\Core\Models\Department;

class DepartmentImporter extends BaseModelImporter
{
    protected static ?string $model = Department::class;
}
