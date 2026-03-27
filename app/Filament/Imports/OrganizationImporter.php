<?php

namespace App\Filament\Imports;

use Modules\Core\Models\Organization;

class OrganizationImporter extends BaseModelImporter
{
    protected static ?string $model = Organization::class;
}
