<?php

namespace Modules\Cms\Filament\Resources\CmsMedia\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cms\Filament\Resources\CmsMedia\CmsMediaResource;

class ListCmsMedia extends ListRecords
{
    protected static string $resource = CmsMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
