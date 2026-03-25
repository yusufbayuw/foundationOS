<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\KpiIndicators\KpiIndicatorResource;

class ViewKpiIndicator extends ViewRecord
{
    protected static string $resource = KpiIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
