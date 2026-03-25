<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;

class ViewUserTenantRole extends ViewRecord
{
    protected static string $resource = UserTenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
