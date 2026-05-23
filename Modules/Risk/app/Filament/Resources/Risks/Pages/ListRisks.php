<?php

namespace Modules\Risk\Filament\Resources\Risks\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\Risks\RiskResource;

class ListRisks extends ListRecords
{
    protected static string $resource = RiskResource::class;
}
