<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\AdmissionPeriodResource;

class ViewAdmissionPeriod extends ViewRecord
{
    protected static string $resource = AdmissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
