<?php

namespace Modules\Asset\Filament\Resources\AssetMaintenances\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetMaintenances\AssetMaintenanceResource;

class ViewAssetMaintenance extends ViewRecord
{
    protected static string $resource = AssetMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
