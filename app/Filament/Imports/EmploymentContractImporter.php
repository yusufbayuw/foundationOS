<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\EmploymentContract;

class EmploymentContractImporter extends BaseModelImporter
{
    protected static ?string $model = EmploymentContract::class;
}
