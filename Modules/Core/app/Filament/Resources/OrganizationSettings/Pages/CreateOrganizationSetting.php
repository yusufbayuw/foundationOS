<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\OrganizationSettings\OrganizationSettingResource;

class CreateOrganizationSetting extends CreateRecord
{
    protected static string $resource = OrganizationSettingResource::class;
}
