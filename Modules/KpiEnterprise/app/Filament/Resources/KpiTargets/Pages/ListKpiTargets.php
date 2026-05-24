<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\KpiTargetResource;

class ListKpiTargets extends ListRecords
{
    protected static string $resource = KpiTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
