<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\KpiCascadeResource;

class ListKpiCascades extends ListRecords
{
    protected static string $resource = KpiCascadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
