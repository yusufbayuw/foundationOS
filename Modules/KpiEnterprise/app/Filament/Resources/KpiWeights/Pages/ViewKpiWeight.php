<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\KpiWeightResource;

class ViewKpiWeight extends ViewRecord
{
    protected static string $resource = KpiWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
