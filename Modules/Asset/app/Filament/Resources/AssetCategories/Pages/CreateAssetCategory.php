<?php

namespace Modules\Asset\Filament\Resources\AssetCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Asset\Filament\Resources\AssetCategories\AssetCategoryResource;

class CreateAssetCategory extends CreateRecord
{
    protected static string $resource = AssetCategoryResource::class;
}
