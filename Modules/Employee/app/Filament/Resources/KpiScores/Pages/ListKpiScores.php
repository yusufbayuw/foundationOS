<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\KpiScores\KpiScoreResource;

class ListKpiScores extends ListRecords
{
    protected static string $resource = KpiScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
