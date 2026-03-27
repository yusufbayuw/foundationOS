<?php

namespace App\Filament\Imports;

use Modules\Core\Models\Tenant;

class TenantImporter extends BaseModelImporter
{
    protected static ?string $model = Tenant::class;
}
