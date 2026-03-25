<?php

namespace Modules\Core\Filament\Resources\TenantModules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\TenantModules\TenantModuleResource;

class ListTenantModules extends ListRecords
{
    protected static string $resource = TenantModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
