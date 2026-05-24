<?php

namespace Modules\Asset\Filament\Resources\AssetInsurances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\AssetInsurances\AssetInsuranceResource;

class ListAssetInsurances extends ListRecords
{
    protected static string $resource = AssetInsuranceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
