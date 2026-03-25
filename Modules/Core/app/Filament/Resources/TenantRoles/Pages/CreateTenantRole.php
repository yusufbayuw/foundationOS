<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\TenantRoles\TenantRoleResource;

class CreateTenantRole extends CreateRecord
{
    protected static string $resource = TenantRoleResource::class;
}
