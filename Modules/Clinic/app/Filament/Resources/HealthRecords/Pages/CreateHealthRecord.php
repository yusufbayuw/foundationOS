<?php

namespace Modules\Clinic\Filament\Resources\HealthRecords\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\HealthRecords\HealthRecordResource;

class CreateHealthRecord extends CreateRecord
{
    protected static string $resource = HealthRecordResource::class;
}
