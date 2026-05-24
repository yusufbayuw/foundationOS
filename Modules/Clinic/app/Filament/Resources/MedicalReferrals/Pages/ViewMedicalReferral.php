<?php

namespace Modules\Clinic\Filament\Resources\MedicalReferrals\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\MedicalReferrals\MedicalReferralResource;

class ViewMedicalReferral extends ViewRecord
{
    protected static string $resource = MedicalReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
