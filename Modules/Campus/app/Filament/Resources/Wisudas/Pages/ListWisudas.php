<?php

namespace Modules\Campus\Filament\Resources\Wisudas\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\Wisudas\WisudaResource;

class ListWisudas extends ListRecords
{
    protected static string $resource = WisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
