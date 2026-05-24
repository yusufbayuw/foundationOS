<?php

namespace Modules\Clinic\Filament\Resources\MedicalConsents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\MedicalConsents\MedicalConsentResource;

class CreateMedicalConsent extends CreateRecord
{
    protected static string $resource = MedicalConsentResource::class;
}
