<?php

namespace Modules\Clinic\Filament\Resources\VaccinationRecords\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\VaccinationRecords\VaccinationRecordResource;

class ListVaccinationRecords extends ListRecords
{
    protected static string $resource = VaccinationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
