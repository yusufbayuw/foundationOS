<?php

namespace Modules\Risk\Filament\Resources\Risks\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Risk\Filament\Resources\Risks\RiskResource;

class CreateRisk extends CreateRecord
{
    protected static string $resource = RiskResource::class;
}
