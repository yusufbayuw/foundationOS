<?php

namespace Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\CapacityUtilizationResource;

class ListCapacityUtilizations extends ListRecords
{
    protected static string $resource = CapacityUtilizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
