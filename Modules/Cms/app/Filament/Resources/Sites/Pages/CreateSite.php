<?php

namespace Modules\Cms\Filament\Resources\Sites\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\Sites\SiteResource;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;
}
