<?php

namespace Modules\Cms\Filament\Resources\PageBlocks\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cms\Filament\Resources\PageBlocks\PageBlockResource;

class ViewPageBlock extends ViewRecord
{
    protected static string $resource = PageBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
