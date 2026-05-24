<?php

namespace Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\InformationAssetResource;

class ListInformationAssets extends ListRecords
{
    protected static string $resource = InformationAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
