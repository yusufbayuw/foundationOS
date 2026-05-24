<?php

namespace Modules\Risk\Filament\Resources\RiskTreatments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Risk\Filament\Resources\RiskTreatments\RiskTreatmentResource;

class ViewRiskTreatment extends ViewRecord
{
    protected static string $resource = RiskTreatmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
