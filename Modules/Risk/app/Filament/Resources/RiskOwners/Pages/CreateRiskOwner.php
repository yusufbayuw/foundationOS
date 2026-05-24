<?php

namespace Modules\Risk\Filament\Resources\RiskOwners\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Risk\Filament\Resources\RiskOwners\RiskOwnerResource;

class CreateRiskOwner extends CreateRecord
{
    protected static string $resource = RiskOwnerResource::class;
}
