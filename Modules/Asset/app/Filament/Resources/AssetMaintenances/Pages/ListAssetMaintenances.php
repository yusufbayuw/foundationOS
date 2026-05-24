<?php

namespace Modules\Asset\Filament\Resources\AssetMaintenances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\AssetMaintenances\AssetMaintenanceResource;

class ListAssetMaintenances extends ListRecords
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
