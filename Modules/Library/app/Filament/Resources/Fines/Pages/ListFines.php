<?php

namespace Modules\Library\Filament\Resources\Fines\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\Fines\FineResource;

class ListFines extends ListRecords
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
