<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\AdmissionPeriodResource;

class CreateAdmissionPeriod extends CreateRecord
{
    protected static string $resource = AdmissionPeriodResource::class;
}
