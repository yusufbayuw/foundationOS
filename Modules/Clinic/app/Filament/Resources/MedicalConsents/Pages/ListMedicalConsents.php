<?php

namespace Modules\Clinic\Filament\Resources\MedicalConsents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\MedicalConsents\MedicalConsentResource;

class ListMedicalConsents extends ListRecords
{
    protected static string $resource = MedicalConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
