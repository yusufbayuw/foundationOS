<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\KpiScores\KpiScoreResource;

class CreateKpiScore extends CreateRecord
{
    protected static string $resource = KpiScoreResource::class;
}
