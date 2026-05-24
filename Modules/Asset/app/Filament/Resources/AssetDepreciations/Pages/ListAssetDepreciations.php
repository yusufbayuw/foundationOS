<?php

namespace Modules\Asset\Filament\Resources\AssetDepreciations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\AssetDepreciations\AssetDepreciationResource;

class ListAssetDepreciations extends ListRecords
{
    protected static string $resource = AssetDepreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
