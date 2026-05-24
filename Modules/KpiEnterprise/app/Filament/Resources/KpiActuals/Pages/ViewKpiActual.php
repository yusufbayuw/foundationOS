<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\KpiActualResource;

class ViewKpiActual extends ViewRecord
{
    protected static string $resource = KpiActualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
