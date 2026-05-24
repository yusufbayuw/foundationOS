<?php

namespace Modules\Clinic\Filament\Resources\VaccinationRecords\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\VaccinationRecordResource;

class ViewVaccinationRecord extends ViewRecord
{
    protected static string $resource = VaccinationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
