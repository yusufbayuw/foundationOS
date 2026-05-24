<?php

namespace Modules\Cms\Filament\Resources\CmsMedia\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cms\Filament\Resources\CmsMedia\CmsMediaResource;

class ViewCmsMedia extends ViewRecord
{
    protected static string $resource = CmsMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
