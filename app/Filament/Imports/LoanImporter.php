<?php

namespace App\Filament\Imports;

use Modules\Library\Models\Loan;

class LoanImporter extends BaseModelImporter
{
    protected static ?string $model = Loan::class;
}
