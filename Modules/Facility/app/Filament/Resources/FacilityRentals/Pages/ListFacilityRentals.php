<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Facility\Filament\Resources\FacilityRentals\FacilityRentalResource;

class ListFacilityRentals extends ListRecords
{
    protected static string $resource = FacilityRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
