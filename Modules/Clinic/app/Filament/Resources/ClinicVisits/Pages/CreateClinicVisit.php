<?php

namespace Modules\Clinic\Filament\Resources\ClinicVisits\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\ClinicVisits\ClinicVisitResource;

class CreateClinicVisit extends CreateRecord
{
    protected static string $resource = ClinicVisitResource::class;
}
