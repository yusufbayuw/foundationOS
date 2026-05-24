<?php

namespace Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\ControlImplementationResource;

class ListControlImplementations extends ListRecords
{
    protected static string $resource = ControlImplementationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
