<?php

namespace Modules\Asset\Filament\Resources\AssetMovements\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetMovements\AssetMovementResource;

class ViewAssetMovement extends ViewRecord
{
    protected static string $resource = AssetMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
