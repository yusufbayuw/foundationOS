<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\KpiTargetResource;

class ViewKpiTarget extends ViewRecord
{
    protected static string $resource = KpiTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
