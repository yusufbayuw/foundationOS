<?php

namespace App\Filament\Imports;

use Modules\Core\Models\Module;

class ModuleImporter extends BaseModelImporter
{
    protected static ?string $model = Module::class;
}
