<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\TenantSettings\TenantSettingResource;

class CreateTenantSetting extends CreateRecord
{
    protected static string $resource = TenantSettingResource::class;
}
