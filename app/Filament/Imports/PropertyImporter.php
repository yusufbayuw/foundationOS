<?php

namespace App\Filament\Imports;

use Modules\Property\Models\Property;

class PropertyImporter extends BaseModelImporter
{
    protected static ?string $model = Property::class;
}