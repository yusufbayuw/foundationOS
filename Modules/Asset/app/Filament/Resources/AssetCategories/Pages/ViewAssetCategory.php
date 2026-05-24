<?php

namespace Modules\Asset\Filament\Resources\AssetCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetCategories\AssetCategoryResource;

class ViewAssetCategory extends ViewRecord
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
