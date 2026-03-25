<?php

namespace Modules\Core\Filament\Resources\TenantModules\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\TenantModules\TenantModuleResource;

class ViewTenantModule extends ViewRecord
{
    protected static string $resource = TenantModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
