<?php

namespace Modules\Cms\Filament\Resources\Sites\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Cms\Filament\Resources\Sites\SiteResource;

class ListSites extends ListRecords
{
    protected static string $resource = SiteResource::class;
}
