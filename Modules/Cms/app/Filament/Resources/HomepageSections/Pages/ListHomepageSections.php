<?php

namespace Modules\Cms\Filament\Resources\HomepageSections\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cms\Filament\Resources\HomepageSections\HomepageSectionResource;

class ListHomepageSections extends ListRecords
{
    protected static string $resource = HomepageSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
