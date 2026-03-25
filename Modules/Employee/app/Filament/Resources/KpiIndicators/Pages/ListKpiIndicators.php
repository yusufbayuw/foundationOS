<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\KpiIndicators\KpiIndicatorResource;

class ListKpiIndicators extends ListRecords
{
    protected static string $resource = KpiIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
