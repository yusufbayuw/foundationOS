<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\SalarySlip;

class SalarySlipImporter extends BaseModelImporter
{
    protected static ?string $model = SalarySlip::class;
}
