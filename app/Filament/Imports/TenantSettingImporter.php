<?php

namespace App\Filament\Imports;

use Modules\Core\Models\TenantSetting;

class TenantSettingImporter extends BaseModelImporter
{
    protected static ?string $model = TenantSetting::class;
}
