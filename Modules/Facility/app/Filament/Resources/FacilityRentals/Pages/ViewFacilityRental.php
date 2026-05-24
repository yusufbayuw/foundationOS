<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\FacilityRentals\FacilityRentalResource;

class ViewFacilityRental extends ViewRecord
{
    protected static string $resource = FacilityRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
