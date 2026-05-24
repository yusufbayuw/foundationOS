<?php

namespace Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\InformationAssetResource;

class ViewInformationAsset extends ViewRecord
{
    protected static string $resource = InformationAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
