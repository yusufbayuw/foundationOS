<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\TenantSettings\TenantSettingResource;

class ListTenantSettings extends ListRecords
{
    protected static string $resource = TenantSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
