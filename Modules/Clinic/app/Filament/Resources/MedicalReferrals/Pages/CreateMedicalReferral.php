<?php

namespace Modules\Clinic\Filament\Resources\MedicalReferrals\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\MedicalReferrals\MedicalReferralResource;

class CreateMedicalReferral extends CreateRecord
{
    protected static string $resource = MedicalReferralResource::class;
}
