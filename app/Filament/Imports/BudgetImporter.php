<?php

namespace App\Filament\Imports;

use Modules\Finance\Models\Budget;

class BudgetImporter extends BaseModelImporter
{
    protected static ?string $model = Budget::class;
}
