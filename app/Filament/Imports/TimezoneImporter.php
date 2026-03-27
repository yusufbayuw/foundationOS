<?php

namespace App\Filament\Imports;

use Modules\Global\Models\Timezone;

class TimezoneImporter extends BaseModelImporter
{
    protected static ?string $model = Timezone::class;
}
