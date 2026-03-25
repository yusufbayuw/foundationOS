<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\TenantRoles\TenantRoleResource;

class ListTenantRoles extends ListRecords
{
    protected static string $resource = TenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
