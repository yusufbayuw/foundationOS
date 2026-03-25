<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\OrganizationSettings\OrganizationSettingResource;

class ViewOrganizationSetting extends ViewRecord
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
