<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\OrganizationSettings\OrganizationSettingResource;

class ListOrganizationSettings extends ListRecords
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
