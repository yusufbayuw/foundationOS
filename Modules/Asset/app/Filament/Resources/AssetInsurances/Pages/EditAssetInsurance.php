<?php

namespace Modules\Asset\Filament\Resources\AssetInsurances\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Asset\Filament\Resources\AssetInsurances\AssetInsuranceResource;

class EditAssetInsurance extends EditRecord
{
    protected static string $resource = AssetInsuranceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
