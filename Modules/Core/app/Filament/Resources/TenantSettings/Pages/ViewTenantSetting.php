<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\TenantSettings\TenantSettingResource;

class ViewTenantSetting extends ViewRecord
{
    protected static string $resource = TenantSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
