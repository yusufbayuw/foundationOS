<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Facility\Filament\Resources\FacilityRentals\FacilityRentalResource;

class CreateFacilityRental extends CreateRecord
{
    protected static string $resource = FacilityRentalResource::class;
}
