<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\PayrollComponent;

class PayrollComponentImporter extends BaseModelImporter
{
    protected static ?string $model = PayrollComponent::class;
}
