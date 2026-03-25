<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;

class CreateUserTenantRole extends CreateRecord
{
    protected static string $resource = UserTenantRoleResource::class;
}
