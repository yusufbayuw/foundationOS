<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\TenantRoles\TenantRoleResource;

class ViewTenantRole extends ViewRecord
{
    protected static string $resource = TenantRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
