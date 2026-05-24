<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\KpiAreaResource;

class ViewKpiArea extends ViewRecord
{
    protected static string $resource = KpiAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
