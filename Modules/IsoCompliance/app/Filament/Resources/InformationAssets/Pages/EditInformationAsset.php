<?php

namespace Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\InformationAssetResource;

class EditInformationAsset extends EditRecord
{
    protected static string $resource = InformationAssetResource::class;

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
