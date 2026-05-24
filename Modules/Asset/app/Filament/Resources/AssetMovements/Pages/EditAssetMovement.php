<?php

namespace Modules\Asset\Filament\Resources\AssetMovements\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Asset\Filament\Resources\AssetMovements\AssetMovementResource;

class EditAssetMovement extends EditRecord
{
    protected static string $resource = AssetMovementResource::class;

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
