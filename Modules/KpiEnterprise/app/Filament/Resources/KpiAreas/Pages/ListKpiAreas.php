<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\KpiAreaResource;

class ListKpiAreas extends ListRecords
{
    protected static string $resource = KpiAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
