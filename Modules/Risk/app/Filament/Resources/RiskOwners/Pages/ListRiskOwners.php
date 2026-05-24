<?php

namespace Modules\Risk\Filament\Resources\RiskOwners\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\RiskOwners\RiskOwnerResource;

class ListRiskOwners extends ListRecords
{
    protected static string $resource = RiskOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
