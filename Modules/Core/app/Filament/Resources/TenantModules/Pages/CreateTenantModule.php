<?php

namespace Modules\Core\Filament\Resources\TenantModules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\TenantModules\TenantModuleResource;

class CreateTenantModule extends CreateRecord
{
    protected static string $resource = TenantModuleResource::class;
}
