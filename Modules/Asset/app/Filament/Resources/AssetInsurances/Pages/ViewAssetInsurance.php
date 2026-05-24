<?php

namespace Modules\Asset\Filament\Resources\AssetInsurances\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetInsurances\AssetInsuranceResource;

class ViewAssetInsurance extends ViewRecord
{
    protected static string $resource = AssetInsuranceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
