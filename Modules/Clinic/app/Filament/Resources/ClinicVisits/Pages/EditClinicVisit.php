<?php

namespace Modules\Clinic\Filament\Resources\ClinicVisits\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Clinic\Filament\Resources\ClinicVisits\ClinicVisitResource;

class EditClinicVisit extends EditRecord
{
    protected static string $resource = ClinicVisitResource::class;
}
