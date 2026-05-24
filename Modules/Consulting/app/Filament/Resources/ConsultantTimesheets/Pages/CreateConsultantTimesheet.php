<?php

namespace Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\ConsultantTimesheetResource;

class CreateConsultantTimesheet extends CreateRecord
{
    protected static string $resource = ConsultantTimesheetResource::class;
}
