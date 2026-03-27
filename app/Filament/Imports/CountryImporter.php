<?php

namespace App\Filament\Imports;

use Modules\Global\Models\Country;

class CountryImporter extends BaseModelImporter
{
    protected static ?string $model = Country::class;
}
