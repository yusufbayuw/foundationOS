<?php

namespace Modules\Clinic\Filament\Resources\InjuryReports\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\InjuryReports\InjuryReportResource;

class CreateInjuryReport extends CreateRecord
{
    protected static string $resource = InjuryReportResource::class;
}
