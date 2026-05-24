<?php

namespace Modules\Risk\Filament\Resources\RiskTreatments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\RiskTreatments\RiskTreatmentResource;

class ListRiskTreatments extends ListRecords
{
    protected static string $resource = RiskTreatmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
