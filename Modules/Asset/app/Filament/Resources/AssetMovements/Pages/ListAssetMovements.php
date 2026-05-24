<?php

namespace Modules\Asset\Filament\Resources\AssetMovements\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\AssetMovements\AssetMovementResource;

class ListAssetMovements extends ListRecords
{
    protected static string $resource = AssetMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
