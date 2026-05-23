<?php

namespace Modules\Clinic\Filament\Resources\ClinicVisits\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\ClinicVisits\ClinicVisitResource;

class ListClinicVisits extends ListRecords
{
    protected static string $resource = ClinicVisitResource::class;
}
