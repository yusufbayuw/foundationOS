<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\AdmissionPeriodResource;

class ListAdmissionPeriods extends ListRecords
{
    protected static string $resource = AdmissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
