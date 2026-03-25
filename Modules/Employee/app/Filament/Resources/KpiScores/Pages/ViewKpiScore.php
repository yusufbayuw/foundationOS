<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\KpiScores\KpiScoreResource;

class ViewKpiScore extends ViewRecord
{
    protected static string $resource = KpiScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
