<?php

namespace App\Filament\Imports;

use Modules\Donation\Models\Donor;

class DonorImporter extends BaseModelImporter
{
    protected static ?string $model = Donor::class;
}
