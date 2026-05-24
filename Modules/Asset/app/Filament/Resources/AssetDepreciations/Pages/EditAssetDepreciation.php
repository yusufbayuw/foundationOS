<?php

namespace Modules\Asset\Filament\Resources\AssetDepreciations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Asset\Filament\Resources\AssetDepreciations\AssetDepreciationResource;

class EditAssetDepreciation extends EditRecord
{
    protected static string $resource = AssetDepreciationResource::class;

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
