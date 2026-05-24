<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\KpiWeightResource;

class ListKpiWeights extends ListRecords
{
    protected static string $resource = KpiWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
