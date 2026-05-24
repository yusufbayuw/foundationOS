<?php

namespace Modules\Clinic\Filament\Resources\MedicalReferrals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\MedicalReferrals\MedicalReferralResource;

class ListMedicalReferrals extends ListRecords
{
    protected static string $resource = MedicalReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
