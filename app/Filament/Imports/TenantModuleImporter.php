<?php

namespace App\Filament\Imports;

use Modules\Core\Models\TenantModule;

class TenantModuleImporter extends BaseModelImporter
{
    protected static ?string $model = TenantModule::class;
}
