<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\KpiActualResource;

class ListKpiActuals extends ListRecords
{
    protected static string $resource = KpiActualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
