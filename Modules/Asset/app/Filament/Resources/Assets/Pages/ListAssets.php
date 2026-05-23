<?php

namespace Modules\Asset\Filament\Resources\Assets\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\Assets\AssetResource;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;
}
