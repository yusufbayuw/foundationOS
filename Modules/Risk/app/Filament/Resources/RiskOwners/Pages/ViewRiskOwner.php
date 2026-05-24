<?php

namespace Modules\Risk\Filament\Resources\RiskOwners\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Risk\Filament\Resources\RiskOwners\RiskOwnerResource;

class ViewRiskOwner extends ViewRecord
{
    protected static string $resource = RiskOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
