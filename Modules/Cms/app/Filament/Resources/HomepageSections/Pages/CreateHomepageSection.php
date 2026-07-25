<?php

namespace Modules\Cms\Filament\Resources\HomepageSections\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\HomepageSections\HomepageSectionResource;

class CreateHomepageSection extends CreateRecord
{
    protected static string $resource = HomepageSectionResource::class;
}
