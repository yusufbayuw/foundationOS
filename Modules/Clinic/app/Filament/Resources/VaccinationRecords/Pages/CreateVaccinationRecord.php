<?php

namespace Modules\Clinic\Filament\Resources\VaccinationRecords\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\VaccinationRecordResource;

class CreateVaccinationRecord extends CreateRecord
{
    protected static string $resource = VaccinationRecordResource::class;
}
