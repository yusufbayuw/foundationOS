<?php

namespace App\Filament\Imports;

use Modules\PhysicalSecurity\Models\Guard;

class GuardImporter extends BaseModelImporter
{
    protected static ?string $model = Guard::class;
}
