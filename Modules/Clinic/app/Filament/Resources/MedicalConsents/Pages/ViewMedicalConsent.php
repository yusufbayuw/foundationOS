<?php

namespace Modules\Clinic\Filament\Resources\MedicalConsents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\MedicalConsents\MedicalConsentResource;

class ViewMedicalConsent extends ViewRecord
{
    protected static string $resource = MedicalConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
