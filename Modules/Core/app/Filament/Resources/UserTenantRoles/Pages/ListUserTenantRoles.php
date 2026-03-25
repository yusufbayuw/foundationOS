<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\UserTenantRoles\UserTenantRoleResource;

class ListUserTenantRoles extends ListRecords
{
    protected static string $resource = UserTenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
