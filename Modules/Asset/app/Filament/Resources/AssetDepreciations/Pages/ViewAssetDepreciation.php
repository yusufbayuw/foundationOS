<?php

namespace Modules\Asset\Filament\Resources\AssetDepreciations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetDepreciations\AssetDepreciationResource;

class ViewAssetDepreciation extends ViewRecord
{
    protected static string $resource = AssetDepreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
