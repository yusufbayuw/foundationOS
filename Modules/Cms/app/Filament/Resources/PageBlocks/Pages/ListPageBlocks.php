<?php

namespace Modules\Cms\Filament\Resources\PageBlocks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cms\Filament\Resources\PageBlocks\PageBlockResource;

class ListPageBlocks extends ListRecords
{
    protected static string $resource = PageBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
